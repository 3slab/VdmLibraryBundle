<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\EventSubscriber;

use Symfony\Component\Messenger\Event\SendMessageToTransportsEvent;
use Vdm\Bundle\LibraryBundle\Model\Message;
use Vdm\Bundle\LibraryBundle\Monitoring\Model\ProducedStat;
use Vdm\Bundle\LibraryBundle\Monitoring\StatsStorageInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class MonitoringWorkerProducedMessageListener implements EventSubscriberInterface
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
     * @param LoggerInterface|null $vdmLogger
     */
    public function __construct(StatsStorageInterface $storage, LoggerInterface $vdmLogger = null)
    {
        $this->storage = $storage;
        $this->logger = $vdmLogger;
    }

    /**
     * Method executed on WorkerMessageHandledEvent event
     *
     * @param SendMessageToTransportsEvent $event
     */
    public function onWorkerMessageProduced(SendMessageToTransportsEvent $event)
    {
        $envelope = $event->getEnvelope();
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
     * @codeCoverageIgnore
     */
    public static function getSubscribedEvents()
    {
        return [
            SendMessageToTransportsEvent::class => 'onWorkerMessageProduced',
        ];
    }
}
