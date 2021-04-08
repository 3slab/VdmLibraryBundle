<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\EventSubscriber\Monitoring;

use Psr\Log\NullLogger;
use Vdm\Bundle\LibraryBundle\Event\CollectWorkerMessageFailedEvent;
use Vdm\Bundle\LibraryBundle\Model\Message;
use Vdm\Bundle\LibraryBundle\Monitoring\StatsStorageInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\Event\WorkerMessageFailedEvent;
use Vdm\Bundle\LibraryBundle\Monitoring\Model\ErrorStat;
use Vdm\Bundle\LibraryBundle\Service\Monitoring\Monitoring;
use Vdm\Bundle\LibraryBundle\Service\Monitoring\MonitoringService;

class MonitoringWorkerMessageFailedSubscriber implements EventSubscriberInterface
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
     * Method executed on WorkerMessageFailedEvent event
     *
     * @param WorkerMessageFailedEvent $event
     */
    public function onWorkerMessageFailedEvent(WorkerMessageFailedEvent $event)
    {
        $this->handleMonitoring();
    }

    /**
     * Method executed on CollectWorkerMessageFailedEvent event
     *
     * @param CollectWorkerMessageFailedEvent $event
     */
    public function onCollectWorkerMessageFailedEvent(CollectWorkerMessageFailedEvent $event)
    {
        $this->handleMonitoring();
    }

    /**
     * Handle increment error stat
     */
    protected function handleMonitoring()
    {
        $this->monitoring->increment(Monitoring::ERROR_STAT, 1);
        $this->logger->debug('error message metric incremented');
    }

    /**
     * {@inheritDoc}
     * @codeCoverageIgnore
     */
    public static function getSubscribedEvents(): array
    {
        return [
            CollectWorkerMessageFailedEvent::class => 'onCollectWorkerMessageFailedEvent',
            WorkerMessageFailedEvent::class => 'onWorkerMessageFailedEvent',
        ];
    }
}
