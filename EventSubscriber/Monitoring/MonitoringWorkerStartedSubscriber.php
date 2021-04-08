<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\EventSubscriber\Monitoring;

use Psr\Log\NullLogger;
use Vdm\Bundle\LibraryBundle\Event\CollectWorkerStartedEvent;
use Vdm\Bundle\LibraryBundle\Monitoring\Model\RunningStat;
use Vdm\Bundle\LibraryBundle\Service\Monitoring\Monitoring;
use Vdm\Bundle\LibraryBundle\Service\Monitoring\MonitoringService;
use Vdm\Bundle\LibraryBundle\Service\Monitoring\Storage\StorageInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\Event\WorkerStartedEvent;

/**
 * Class MonitoringWorkerStartedSubscriber
 *
 * @package Vdm\Bundle\LibraryBundle\EventSubscriber\Monitoring
 */
class MonitoringWorkerStartedSubscriber implements EventSubscriberInterface
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
     * MonitoringWorkerHandledMessageListener constructor.
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
     * Method executed on WorkerStartedEvent event
     *
     * @param WorkerStartedEvent $event
     */
    public function onWorkerStartedEvent(WorkerStartedEvent $event)
    {
        $this->handleMonitoring();
    }

    /**
     * Method executed on CollectWorkerStartedEvent event
     *
     * @param CollectWorkerStartedEvent $event
     */
    public function onCollectWorkerStartedEvent(CollectWorkerStartedEvent $event)
    {
        $this->handleMonitoring();
    }

    /**
     * Set running stat
     */
    protected function handleMonitoring(): void
    {
        $this->monitoring->update(Monitoring::RUNNING_STAT, 1);
        $this->logger->debug('worker started metric sent');
    }

    /**
     * {@inheritDoc}
     * @codeCoverageIgnore
     */
    public static function getSubscribedEvents(): array
    {
        return [
            CollectWorkerStartedEvent::class => 'onCollectWorkerStartedEvent',
            WorkerStartedEvent::class => 'onWorkerStartedEvent',
        ];
    }
}
