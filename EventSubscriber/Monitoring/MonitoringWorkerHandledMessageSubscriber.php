<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\EventSubscriber\Monitoring;

use Psr\Log\NullLogger;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Vdm\Bundle\LibraryBundle\Event\CollectWorkerMessageHandledEvent;
use Vdm\Bundle\LibraryBundle\Monitoring\Model\HandledStat;
use Vdm\Bundle\LibraryBundle\Monitoring\StatsStorageInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\Event\WorkerMessageHandledEvent;
use Vdm\Bundle\LibraryBundle\Service\Monitoring\Monitoring;
use Vdm\Bundle\LibraryBundle\Service\Monitoring\MonitoringService;

/**
 * Class MonitoringWorkerHandledMessageSubscriber
 *
 * @package Vdm\Bundle\LibraryBundle\EventSubscriber\Monitoring
 */
class MonitoringWorkerHandledMessageSubscriber implements EventSubscriberInterface
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
     * Method executed on WorkerMessageHandledEvent event
     *
     * @param WorkerMessageHandledEvent $event
     */
    public function onWorkerMessageHandledEvent(WorkerMessageHandledEvent $event)
    {
        $this->handleMonitoring($event->getEnvelope());
    }

    /**
     * Method executed on CollectWorkerMessageHandledEvent event
     *
     * @param CollectWorkerMessageHandledEvent $event
     */
    public function onCollectWorkerMessageHandledEvent(CollectWorkerMessageHandledEvent $event)
    {
        $this->handleMonitoring($event->getEnvelope());
    }

    /**
     * Increment handled stat in monitoring
     *
     * @param Envelope $envelope
     */
    protected function handleMonitoring(Envelope $envelope)
    {
        if (!$this->isMessageHandled($envelope)) {
            return;
        }

        $this->monitoring->increment(Monitoring::HANDLED_STAT, 1);
        $this->logger->debug('handled message metric incremented');
    }

    /**
     * {@inheritDoc}
     * @codeCoverageIgnore
     */
    public static function getSubscribedEvents(): array
    {
        return [
            CollectWorkerMessageHandledEvent::class => 'onCollectWorkerMessageHandledEvent',
            WorkerMessageHandledEvent::class => 'onWorkerMessageHandledEvent',
        ];
    }

    /**
     * Check if envelope has HandleStamp
     *
     * @param Envelope $envelope
     *
     * @return bool
     */
    protected function isMessageHandled(Envelope $envelope): bool
    {
        $handledStamp = $envelope->last(HandledStamp::class);
        if (!$handledStamp) {
            return false;
        }

        return true;
    }
}
