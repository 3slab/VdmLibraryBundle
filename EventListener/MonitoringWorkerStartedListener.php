<?php


namespace App\EventListener;


use App\Monitoring\Model\RunningStat;
use App\Monitoring\StatsStorageInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\Event\WorkerStartedEvent;

class MonitoringWorkerStartedListener implements EventSubscriberInterface
{
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
     * @param StatsStorageInterface $storage
     * @param LoggerInterface|null $messengerLogger
     */
    public function __construct(StatsStorageInterface $storage, LoggerInterface $messengerLogger = null)
    {
        $this->storage = $storage;
        $this->logger = $messengerLogger;
    }

    /**
     * Method executed on WorkerStartedEvent event
     *
     * @param WorkerStartedEvent $event
     */
    public function onWorkerStarted(WorkerStartedEvent $event)
    {
        // Send worker running stat
        $runningStat = new RunningStat(true);
        $this->storage->sendRunningStat($runningStat);

        if (null !== $this->logger) {
            $this->logger->info('WorkerStartedEvent - Running stats sent - {isRunning}',
                [
                    'isRunning' => $runningStat->isRunning() ?: '0'
                ]
            );
        }
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            WorkerStartedEvent::class => 'onWorkerStarted',
        ];
    }
}
