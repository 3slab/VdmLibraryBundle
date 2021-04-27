<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\EventSubscriber\Monitoring;

use Psr\Log\NullLogger;
use Symfony\Component\Messenger\Event\WorkerStoppedEvent;
use Vdm\Bundle\LibraryBundle\Event\CollectWorkerStoppedEvent;
use Vdm\Bundle\LibraryBundle\Monitoring\Model\RunningStat;
use Vdm\Bundle\LibraryBundle\Service\Monitoring\Monitoring;
use Vdm\Bundle\LibraryBundle\Service\Monitoring\MonitoringService;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class MonitoringWorkerStoppedSubscriber
 *
 * @package Vdm\Bundle\LibraryBundle\EventSubscriber\Monitoring
 */
class MonitoringWorkerStoppedSubscriber implements EventSubscriberInterface
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
     * MonitoringWorkerTerminateSubscriber constructor.
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
     * Method executed on ConsoleTerminateEvent event
     *
     * @param WorkerStoppedEvent $event
     */
    public function onWorkerStoppedEvent(WorkerStoppedEvent $event)
    {
        $this->handleMonitoring();
    }

    /**
     * Method executed on CollectWorkerStoppedEvent event
     *
     * @param WorkerStoppedEvent $event
     */
    public function onCollectWorkerStoppedEvent(CollectWorkerStoppedEvent $event)
    {
        $this->handleMonitoring();
    }

    /**
     * Set worker stopped state and flush monitoring stat to storage
     */
    protected function handleMonitoring()
    {
        $this->monitoring->update(Monitoring::RUNNING_STAT, 0);
        $this->logger->debug('worker stopped metric sent');

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
            WorkerStoppedEvent::class => 'onWorkerStoppedEvent',
            CollectWorkerStoppedEvent::class => 'onCollectWorkerStoppedEvent',
        ];
    }
}
