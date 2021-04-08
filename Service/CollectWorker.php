<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\LegacyEventDispatcherProxy;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\Exception\RejectRedeliveredMessageException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\ReceivedStamp;
use Symfony\Component\Messenger\Transport\Receiver\ReceiverInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Vdm\Bundle\LibraryBundle\Event\CollectWorkerMessageFailedEvent;
use Vdm\Bundle\LibraryBundle\Event\CollectWorkerMessageHandledEvent;
use Vdm\Bundle\LibraryBundle\Event\CollectWorkerMessageReceivedEvent;
use Vdm\Bundle\LibraryBundle\Event\CollectWorkerRunningEvent;
use Vdm\Bundle\LibraryBundle\Event\CollectWorkerStartedEvent;
use Vdm\Bundle\LibraryBundle\Event\CollectWorkerStoppedEvent;
use Vdm\Bundle\LibraryBundle\Stamp\CollectedByWorkerStamp;

class CollectWorker
{
    private $receivers;
    private $bus;
    private $eventDispatcher;
    private $logger;
    private $shouldStop = false;

    /**
     * @param ReceiverInterface[] $receivers Where the key is the transport name
     * @param MessageBusInterface $bus
     * @param EventDispatcherInterface|null $eventDispatcher
     * @param LoggerInterface|null $logger
     */
    public function __construct(
        array $receivers,
        MessageBusInterface $bus,
        EventDispatcherInterface $eventDispatcher = null,
        LoggerInterface $logger = null
    ) {
        $this->receivers = $receivers;
        $this->bus = $bus;
        $this->logger = $logger;
        if (class_exists(Event::class)) {
            $this->eventDispatcher = LegacyEventDispatcherProxy::decorate($eventDispatcher);
        } else {
            $this->eventDispatcher = $eventDispatcher;
        }
    }

    /**
     * Collect the messages and dispatch them to the bus.
     */
    public function run(): void
    {
        $this->dispatchEvent(new CollectWorkerStartedEvent($this));

        foreach ($this->receivers as $transportName => $receiver) {
            $envelopes = $receiver->get();

            foreach ($envelopes as $envelope) {
                $this->handleMessage($envelope, $receiver, $transportName);
                $this->dispatchEvent(new CollectWorkerRunningEvent($this, false));

                if ($this->shouldStop) {
                    break 2;
                }
            }
        }

        $this->dispatchEvent(new CollectWorkerStoppedEvent($this));
    }

    /**
     * Simulate everything like the default consume message when collecting from
     * non "consumable" transport provided by VDM
     *
     * @param Envelope $envelope
     * @param ReceiverInterface $receiver
     * @param string $transportName
     */
    private function handleMessage(Envelope $envelope, ReceiverInterface $receiver, string $transportName): void
    {
        $event = new CollectWorkerMessageReceivedEvent($envelope, $transportName);
        $this->dispatchEvent($event);
        $envelope = $event->getEnvelope();

        if (!$event->shouldHandle()) {
            return;
        }

        try {
            $envelope = $this->bus->dispatch(
                $envelope->with(new ReceivedStamp($transportName), new CollectedByWorkerStamp())
            );
        } catch (\Throwable $throwable) {
            $rejectFirst = $throwable instanceof RejectRedeliveredMessageException;
            if ($rejectFirst) {
                // redelivered messages are rejected first so that continuous failures in an event listener or while
                // publishing for retry does not cause infinite redelivery loops
                $receiver->reject($envelope);
            }

            if ($throwable instanceof HandlerFailedException) {
                $envelope = $throwable->getEnvelope();
            }

            $failedEvent = new CollectWorkerMessageFailedEvent($envelope, $transportName, $throwable);
            $this->dispatchEvent($failedEvent);
            $envelope = $failedEvent->getEnvelope();

            if (!$rejectFirst) {
                $receiver->reject($envelope);
            }

            return;
        }

        $handledEvent = new CollectWorkerMessageHandledEvent($envelope, $transportName);
        $this->dispatchEvent($handledEvent);
        $envelope = $handledEvent->getEnvelope();

        if (null !== $this->logger) {
            $message = $envelope->getMessage();
            $context = [
                'message' => $message,
                'class' => \get_class($message),
            ];
            $this->logger->info('{class} was handled successfully (acknowledging to transport).', $context);
        }

        $receiver->ack($envelope);
    }

    /**
     * Set stop flag on worker
     */
    public function stop(): void
    {
        $this->shouldStop = true;
    }

    /**
     * @param object $event
     */
    private function dispatchEvent(object $event): void
    {
        if (null === $this->eventDispatcher) {
            return;
        }

        $this->eventDispatcher->dispatch($event);
    }
}
