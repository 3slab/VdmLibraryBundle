<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\EventListener;

use Vdm\Bundle\LibraryBundle\Monitoring\Model\ErrorStateStat;
use Vdm\Bundle\LibraryBundle\Monitoring\StatsStorageInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\Event\WorkerRunningEvent;

class MonitoringWorkerRunningListener implements EventSubscriberInterface
{
    /**
     * @var ErrorDuringMessageHandlerListener
     */
    private $trackerErrorListener;

    /**
     * @var StatsStorageInterface
     */
    private $storage;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * MonitoringWorkerHandledMessageListener constructor.
     *
     * @param ErrorDuringMessageHandlerListener $trackerErrorListener
     * @param StatsStorageInterface $storage
     * @param LoggerInterface|null $messengerLogger
     */
    public function __construct(
        ErrorDuringMessageHandlerListener $trackerErrorListener,
        StatsStorageInterface $storage,
        LoggerInterface $messengerLogger = null
    ) {
        $this->trackerErrorListener = $trackerErrorListener;
        $this->storage = $storage;
        $this->logger = $messengerLogger;
    }

    /**
     * Method executed on onWorkerRunning event
     *
     * @param WorkerRunningEvent $event
     */
    public function onWorkerRunning(WorkerRunningEvent $event)
    {
        // An error already occured. Don't reset the error state
        $throwable = $this->trackerErrorListener->getThrownException();
        if ($throwable) {
            return;
        }

        // Reset error state state because message successfully handled if we reached this event
        $errorStateStat = new ErrorStateStat();
        $this->storage->sendErrorStateStat($errorStateStat);

        if (null !== $this->logger) {
            $this->logger->info('WorkerRunningEvent - Error state stats sent with code {code}',
                [
                    'code' => $errorStateStat->getCode()
                ]
            );
        }

        if (null !== $this->logger) {
            $this->logger->info('WorkerRunningEvent - Stats storage flushed');
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
