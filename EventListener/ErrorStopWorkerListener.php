<?php

namespace Vdm\Bundle\LibraryBundle\EventListener;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\Event\WorkerRunningEvent;
use Symfony\Component\Messenger\Event\WorkerStartedEvent;
use Symfony\Component\Messenger\Exception\RuntimeException;

class ErrorStopWorkerListener implements EventSubscriberInterface
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
     * ErrorStopWorkerListener constructor.
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
     * Method executed on WorkerRunningEvent event
     *
     * @param WorkerRunningEvent $event
     */
    public function onWorkerRunning(WorkerRunningEvent $event): void
    {
        $throwable = $this->trackerErrorListener->getThrownException();
        if (!$throwable) {
            return;
        }

        $event->getWorker()->stop();

        if (null !== $this->logger) {
            $this->logger->info('WorkerRunningEvent - Worker stopping because an error happened during handling');
        }
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            WorkerRunningEvent::class => 'onWorkerRunning',
        ];
    }
}
