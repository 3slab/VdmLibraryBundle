<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\EventListener;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Vdm\Bundle\LibraryBundle\Model\Message;
use Vdm\Bundle\LibraryBundle\Monitoring\Model\HandledStat;
use Vdm\Bundle\LibraryBundle\Monitoring\StatsStorageInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\Event\WorkerMessageHandledEvent;

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

        if (!$this->isMessageSent($envelope)) {
            return;
        }

        $message = $envelope->getMessage();

        if (!$message instanceof Message) {
            return;
        }

        // Send produced stats because we check for sentstamp above
        $handledStat = new HandledStat(1);
        $this->storage->sendHandledStat($handledStat);

        if (null !== $this->logger) {
            $this->logger->info('WorkerMessageHandledEvent - Handled stats sent');
        }
    }

    /**
     * {@inheritDoc}
     * @codeCoverageIgnore
     */
    public static function getSubscribedEvents()
    {
        return [
            WorkerMessageHandledEvent::class => 'onWorkerMessageHandled',
        ];
    }

    /**
     * Check if enveloppe has HandleStamp
     *
     * @param Envelope $envelope
     *
     * @return bool
     */
    protected function isMessageSent(Envelope  $envelope): bool
    {
        $handledStamp = $envelope->last(HandledStamp::class);

        if (!$handledStamp) {
            return false;
        }

        return true;
    }
}
