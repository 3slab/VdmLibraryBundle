<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\EventSubscriber\Monitoring;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\Event\WorkerRunningEvent;
use Vdm\Bundle\LibraryBundle\Event\CollectWorkerRunningEvent;
use Vdm\Bundle\LibraryBundle\Service\Monitoring\MonitoringService;

class MonitoringWorkerRunningFlushSubscriber implements EventSubscriberInterface
{
    /**
     * @var MonitoringService
     */
    protected $monitoring;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * MonitoringWorkerRunningFlushSubscriber constructor.
     *
     * @param MonitoringService $monitoring
     * @param LoggerInterface|null $vdmLogger
     */
    public function __construct(MonitoringService $monitoring, LoggerInterface $vdmLogger = null)
    {
        $this->monitoring = $monitoring;
        $this->logger = $vdmLogger ?? new NullLogger();
    }

    /**
     * Method executed on WorkerRunning event
     *
     * @param WorkerRunningEvent $event
     */
    public function onWorkerRunningEvent(WorkerRunningEvent $event)
    {
        $this->handleMonitoring();
    }

    /**
     * Method executed on CollectWorkerRunning event
     *
     * @param CollectWorkerRunningEvent $event
     */
    public function onCollectWorkerRunningEvent(CollectWorkerRunningEvent $event)
    {
        $this->handleMonitoring();
    }

    /**
     * Flush monitoring stat to storage
     */
    protected function handleMonitoring()
    {
        $this->monitoring->flush();
        $this->logger->debug('metric storage flushed');
    }

    /**
     * {@inheritDoc}
     * @codeCoverageIgnore
     */
    public static function getSubscribedEvents(): array
    {
        return [
            CollectWorkerRunningEvent::class => 'onCollectWorkerRunningEvent',
            WorkerRunningEvent::class => 'onWorkerRunningEvent',
        ];
    }
}
