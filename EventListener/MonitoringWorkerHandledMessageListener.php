<?php


namespace App\EventListener;


use App\Model\Message;
use App\Monitoring\Model\ProducedStat;
use App\Monitoring\StatsStorageInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\Event\WorkerMessageHandledEvent;
use Symfony\Component\Messenger\Stamp\SentStamp;

class MonitoringWorkerHandledMessageListener implements EventSubscriberInterface
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
     * Method executed on WorkerMessageHandledEvent event
     *
     * @param WorkerMessageHandledEvent $event
     */
    public function onWorkerMessageHandled(WorkerMessageHandledEvent $event)
    {
        $envelope = $event->getEnvelope();
        if (!$envelope->last(SentStamp::class)) {
            return;
        }

        $message = $envelope->getMessage();
        if (!$message instanceof Message) {
            return;
        }

        // Send produced stats because we check for sentstamp above
        $producedStat = new ProducedStat(1);
        $this->storage->sendProducedStat($producedStat);

        if (null !== $this->logger) {
            $this->logger->info('WorkerMessageHandledEvent - Produced stats sent');
        }
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            WorkerMessageHandledEvent::class => 'onWorkerMessageHandled',
        ];
    }
}
