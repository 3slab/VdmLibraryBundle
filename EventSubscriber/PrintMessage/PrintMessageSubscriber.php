<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\EventSubscriber\PrintMessage;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Event\SendMessageToTransportsEvent;
use Symfony\Component\Messenger\Event\WorkerMessageReceivedEvent;
use Vdm\Bundle\LibraryBundle\Event\CollectWorkerMessageReceivedEvent;

/**
 * Class PrintMessageSubscriber
 * @package Vdm\Bundle\LibraryBundle\EventSubscriber\PrintMessage
 */
class PrintMessageSubscriber implements EventSubscriberInterface
{
    /**
     * @var bool
     */
    protected $printMsg;

    /**
     * @var bool
     */
    protected $logMsg;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * PrintMessageSubscriber constructor.
     * @param bool $printMsg
     * @param bool $logMsg
     * @param LoggerInterface|null $vdmLogger
     */
    public function __construct(bool $printMsg = false, bool $logMsg = false, LoggerInterface $vdmLogger = null)
    {
        $this->printMsg = $printMsg;
        $this->logMsg = $logMsg;
        $this->logger = $vdmLogger ?? new NullLogger();
    }

    /**
     * Method executed on CollectWorkerMessageReceivedEvent event
     *
     * @param CollectWorkerMessageReceivedEvent $event
     */
    public function onCollectWorkerMessageReceivedEvent(CollectWorkerMessageReceivedEvent $event)
    {
        $this->printMessageInEnvelope($event->getEnvelope());
    }

    /**
     * Method executed on WorkerMessageReceivedEvent event
     *
     * @param WorkerMessageReceivedEvent $event
     */
    public function onWorkerMessageReceivedEvent(WorkerMessageReceivedEvent $event)
    {
        $this->printMessageInEnvelope($event->getEnvelope());
    }

    /**
     * Method executed on SendMessageToTransportsEvent event
     *
     * @param SendMessageToTransportsEvent $event
     */
    public function onSendMessageToTransportEvent(SendMessageToTransportsEvent $event)
    {
        $this->printMessageInEnvelope($event->getEnvelope());
    }

    /**
     * @param Envelope $envelope
     */
    public function printMessageInEnvelope(Envelope $envelope)
    {
        $message = $envelope->getMessage();

        if ($this->printMsg) {
            echo '-------------------------'.PHP_EOL;
            echo '---- dumping message ----'.PHP_EOL;
            echo '-------------------------'.PHP_EOL;
            dump($message);
            echo '-------------------------'.PHP_EOL;
            echo '-------------------------'.PHP_EOL;
        }

        if ($this->logMsg) {
            $this->logger->debug('{message}', ['message' => $message]);
        }
    }

    /**
     * {@inheritDoc}
     * @codeCoverageIgnore
     */
    public static function getSubscribedEvents(): array
    {
        // Ensure that this subscriber run before/after all the others
        return [
            CollectWorkerMessageReceivedEvent::class => ['onCollectWorkerMessageReceivedEvent', 9999],
            WorkerMessageReceivedEvent::class => ['onWorkerMessageReceivedEvent', 9999],
            SendMessageToTransportsEvent::class => ['onSendMessageToTransportEvent', -9999],
        ];
    }
}
