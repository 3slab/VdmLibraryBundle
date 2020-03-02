<?php

namespace App\EventListener;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\Event\WorkerStoppedEvent;
use Symfony\Component\Messenger\Exception\RuntimeException;

class ErrorRethrowAfterWorkerStoppedListener implements EventSubscriberInterface
{
    /**
     * @var LoggerInterface|null
     */
    private $logger;

    /**
     * @var ErrorDuringMessageHandlerListener
     */
    private $trackerErrorListener;

    /**
     * ErrorRethrowAfterWorkerStoppedListener constructor.
     *
     * @param ErrorDuringMessageHandlerListener $trackerErrorListener
     * @param LoggerInterface|null $messengerLogger
     */
    public function __construct(ErrorDuringMessageHandlerListener $trackerErrorListener, LoggerInterface $messengerLogger = null)
    {
        $this->trackerErrorListener = $trackerErrorListener;
        $this->logger = $messengerLogger;
    }

    /**
     * Method executed on WorkerStoppedEvent event
     *
     * @param WorkerStoppedEvent $event
     */
    public function onWorkerStopped(WorkerStoppedEvent $event): void
    {
        $throwable = $this->trackerErrorListener->getThrownException();
        if (!$throwable) {
            return;
        }

        if (null !== $this->logger) {
            $this->logger->info(
                'WorkerStoppedEvent- Worker stopped because an error happened' .
                ' during handling. Rethrowing exception ...'
            );
        }

        // Rethrow caught exception in handler after worker stopped to exit cli with an error code different from 0
        throw new RuntimeException('Worker stopped because of handler exception', 1, $throwable);
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            // Execute after monitoring tracking listener
            WorkerStoppedEvent::class => ['onWorkerStopped', -200],
        ];
    }
}
