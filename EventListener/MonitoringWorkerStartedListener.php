<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\EventListener;

use Vdm\Bundle\LibraryBundle\Monitoring\Model\RunningStat;
use Vdm\Bundle\LibraryBundle\Monitoring\StatsStorageInterface;
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
     * @param LoggerInterface|null $vdmLogger
     */
    public function __construct(StatsStorageInterface $storage, LoggerInterface $vdmLogger = null)
    {
        $this->storage = $storage;
        $this->logger = $vdmLogger;
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
     * @codeCoverageIgnore
     */
    public static function getSubscribedEvents()
    {
        return [
            WorkerStartedEvent::class => 'onWorkerStarted',
        ];
    }
}
