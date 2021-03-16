<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\EventSubscriber;

use Vdm\Bundle\LibraryBundle\Monitoring\StatsStorageInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\Event\WorkerMessageReceivedEvent;
use Symfony\Component\Messenger\Event\WorkerMessageHandledEvent;
use Symfony\Component\Messenger\Event\WorkerMessageFailedEvent;
use Symfony\Component\Stopwatch\Stopwatch;
use Psr\Log\LoggerInterface;
use Vdm\Bundle\LibraryBundle\Monitoring\Model\MemoryStat;

class MonitoringWorkerHandleMessageMemorySubscriber implements EventSubscriberInterface
{
    /**
     * @var StatsStorageInterface
     */
    private $storage;

    /**
     * @var Stopwatch $stopwatch
     */
    private $stopwatch;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * MonitoringWorkerSubscriber constructor.
     *
     * @param StatsStorageInterface $storage
     * @param LoggerInterface|null $vdmLogger
     */
    public function __construct(StatsStorageInterface $storage, LoggerInterface $vdmLogger = null)
    {
        $this->storage = $storage;
        $this->stopwatch = new Stopwatch();
        $this->logger = $vdmLogger;
    }

    /**
     * Method executed on WorkerMessageReceivedEvent event
     *
     * @param WorkerMessageReceivedEvent $event
     */
    public function onWorkerMessageReceived(WorkerMessageReceivedEvent $event)
    {
        $this->stopwatch->reset();
        $this->stopwatch->start('WorkerEvent');
        $this->logger->debug('WorkerStartedEvent - Running memory begin');
    }

    /**
     * Method executed on WorkerMessageFailedEvent event
     *
     * @param WorkerMessageFailedEvent $event
     */
    public function onWorkerMessageFailed(WorkerMessageFailedEvent $event)
    {
        $this->memoryStatStorage();
    }

    /**
     * Method executed on WorkerMessageHandledEvent event
     *
     * @param WorkerMessageHandledEvent $event
     */
    public function onWorkerMessageHandled(WorkerMessageHandledEvent $event): void
    {
        $this->memoryStatStorage();
    }

    /**
     * {@inheritDoc}
     * @codeCoverageIgnore
     */
    public static function getSubscribedEvents()
    {
        return [
            WorkerMessageReceivedEvent::class => 'onWorkerMessageReceived',
            WorkerMessageHandledEvent::class => 'onWorkerMessageHandled',
            WorkerMessageFailedEvent::class => 'onWorkerMessageFailed',
        ];
    }

    private function memoryStatStorage()
    {
        $eventStopwatch = $this->stopwatch->stop('WorkerEvent');

        $memoryStat = new MemoryStat($eventStopwatch->getMemory());
        $this->storage->sendMemoryStat($memoryStat);

        $this->logger->info('WorkerStartedEvent - Running memory {memory} octets',
            [
                'memory' => $eventStopwatch->getMemory()
            ]
        );
    }
}
