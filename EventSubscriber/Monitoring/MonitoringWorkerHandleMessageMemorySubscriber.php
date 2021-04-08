<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\EventSubscriber\Monitoring;

use Psr\Log\NullLogger;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\Event\WorkerMessageReceivedEvent;
use Symfony\Component\Messenger\Event\WorkerMessageHandledEvent;
use Symfony\Component\Messenger\Event\WorkerMessageFailedEvent;
use Symfony\Component\Stopwatch\Stopwatch;
use Psr\Log\LoggerInterface;
use Vdm\Bundle\LibraryBundle\Event\CollectWorkerMessageFailedEvent;
use Vdm\Bundle\LibraryBundle\Event\CollectWorkerMessageHandledEvent;
use Vdm\Bundle\LibraryBundle\Event\CollectWorkerMessageReceivedEvent;
use Vdm\Bundle\LibraryBundle\Service\Monitoring\Monitoring;
use Vdm\Bundle\LibraryBundle\Service\Monitoring\MonitoringService;

class MonitoringWorkerHandleMessageMemorySubscriber implements EventSubscriberInterface
{
    /**
     * @var MonitoringService
     */
    protected $monitoring;

    /**
     * @var Stopwatch $stopwatch
     */
    protected $stopwatch;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * MonitoringWorkerHandleMessageMemorySubscriber constructor.
     *
     * @param MonitoringService $monitoring
     * @param LoggerInterface|null $vdmLogger
     */
    public function __construct(MonitoringService $monitoring, LoggerInterface $vdmLogger = null)
    {
        $this->stopwatch = new Stopwatch();
        $this->monitoring = $monitoring;
        $this->logger = $vdmLogger ?? new NullLogger();
    }

    /**
     * Method executed on WorkerMessageReceivedEvent event
     *
     * @param WorkerMessageReceivedEvent $event
     */
    public function onWorkerMessageReceivedEvent(WorkerMessageReceivedEvent $event)
    {
        $this->memoryStatInit();
    }

    /**
     * Method executed on WorkerMessageFailedEvent event
     *
     * @param WorkerMessageFailedEvent $event
     */
    public function onWorkerMessageFailedEvent(WorkerMessageFailedEvent $event)
    {
        $this->memoryStatStorage();
    }

    /**
     * Method executed on WorkerMessageHandledEvent event
     *
     * @param WorkerMessageHandledEvent $event
     */
    public function onWorkerMessageHandledEvent(WorkerMessageHandledEvent $event): void
    {
        $this->memoryStatStorage();
    }

    /**
     * Method executed on CollectWorkerMessageReceivedEvent event
     *
     * @param CollectWorkerMessageReceivedEvent $event
     */
    public function onCollectWorkerMessageReceivedEvent(CollectWorkerMessageReceivedEvent $event)
    {
        $this->memoryStatInit();
    }

    /**
     * Method executed on CollectWorkerMessageFailedEvent event
     *
     * @param CollectWorkerMessageFailedEvent $event
     */
    public function onCollectWorkerMessageFailedEvent(CollectWorkerMessageFailedEvent $event)
    {
        $this->memoryStatStorage();
    }

    /**
     * Method executed on CollectWorkerMessageHandledEvent event
     *
     * @param CollectWorkerMessageHandledEvent $event
     */
    public function onCollectWorkerMessageHandledEvent(CollectWorkerMessageHandledEvent $event): void
    {
        $this->memoryStatStorage();
    }

    /**
     * {@inheritDoc}
     * @codeCoverageIgnore
     */
    public static function getSubscribedEvents(): array
    {
        return [
            CollectWorkerMessageReceivedEvent::class => 'onCollectWorkerMessageReceivedEvent',
            CollectWorkerMessageHandledEvent::class => 'onCollectWorkerMessageHandledEvent',
            CollectWorkerMessageFailedEvent::class => 'onCollectWorkerMessageFailedEvent',
            WorkerMessageReceivedEvent::class => 'onWorkerMessageReceivedEvent',
            WorkerMessageHandledEvent::class => 'onWorkerMessageHandledEvent',
            WorkerMessageFailedEvent::class => 'onWorkerMessageFailedEvent',
        ];
    }

    /**
     * Init memory metric collection
     */
    protected function memoryStatInit()
    {
        $this->stopwatch->reset();
        $this->stopwatch->start('memory');
        $this->logger->debug('memory metric start collection');
    }

    /**
     * Handle memory metric collection
     */
    protected function memoryStatStorage(): void
    {
        $memory = $this->stopwatch->stop('memory')->getMemory();
        $this->monitoring->update(Monitoring::MEMORY_STAT, $memory);
        $this->logger->debug('memory metric collected : {memory}', [
            'memory' => $memory
        ]);
    }
}
