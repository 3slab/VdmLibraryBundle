<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\EventListener;

use Vdm\Bundle\LibraryBundle\Monitoring\StatsStorageInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\Event\WorkerMessageReceivedEvent;
use Symfony\Component\Messenger\Event\WorkerMessageHandledEvent;
use Symfony\Component\Messenger\Event\WorkerMessageFailedEvent;
use Symfony\Component\Stopwatch\Stopwatch;
use Psr\Log\LoggerInterface;
use Vdm\Bundle\LibraryBundle\Monitoring\Model\TimeStat;

class MonitoringWorkerHandleMessageTimeSubscriber implements EventSubscriberInterface
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
        $this->logger->debug('WorkerStartedEvent - Running time begin');
    }

    /**
     * Method executed on WorkerMessageFailedEvent event
     *
     * @param WorkerMessageFailedEvent $event
     */
    public function onWorkerMessageFailed(WorkerMessageFailedEvent $event)
    {
        $this->timeStatStorage();
    }

    /**
     * Method executed on WorkerMessageHandledEvent event
     *
     * @param WorkerMessageHandledEvent $event
     */
    public function onWorkerMessageHandled(WorkerMessageHandledEvent $event): void
    {
        $this->timeStatStorage();
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

    private function timeStatStorage()
    {
        $eventStopwatch = $this->stopwatch->stop('WorkerEvent');

        $timeStat = new TimeStat($eventStopwatch->getDuration());
        $this->storage->sendTimeStat($timeStat);

        $this->logger->info('WorkerStartedEvent - Running time {end} milliseconds',
            [
                'end' => $eventStopwatch->getDuration()
            ]
        );
    }
}
