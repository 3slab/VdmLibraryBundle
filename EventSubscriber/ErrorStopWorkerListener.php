<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\EventSubscriber;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
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
     * @param LoggerInterface|null $vdmLogger
     */
    public function __construct(ErrorDuringMessageHandlerListener $trackerErrorListener, LoggerInterface $vdmLogger = null)
    {
        $this->trackerErrorListener = $trackerErrorListener;
        $this->logger = $vdmLogger ?? new NullLogger();
    }

    /**
     * Method executed on WorkerRunningEvent event
     *
     * @param WorkerRunningEvent $event
     */
    public function onWorkerRunning(WorkerRunningEvent $event): void
    {
        $throwable = $this->trackerErrorListener->getThrownException();
        if (!($throwable instanceof \Throwable)) {
            return;
        }

        $event->getWorker()->stop();

        $this->logger->info('WorkerRunningEvent - Worker stopping because an error happened during handling');
    }

    /**
     * {@inheritDoc}
     * @codeCoverageIgnore
     */
    public static function getSubscribedEvents()
    {
        return [
            WorkerRunningEvent::class => 'onWorkerRunning',
        ];
    }
}
