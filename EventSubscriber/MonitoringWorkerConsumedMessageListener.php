<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\EventSubscriber;

use Vdm\Bundle\LibraryBundle\Model\Message;
use Vdm\Bundle\LibraryBundle\Monitoring\Model\ConsumerStat;
use Vdm\Bundle\LibraryBundle\Monitoring\StatsStorageInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\Event\WorkerMessageReceivedEvent;

class MonitoringWorkerConsumedMessageListener implements EventSubscriberInterface
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
     * MonitoringWorkerConsumedMessageListener constructor.
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
     * Method executed on WorkerMessageReceivedEvent event
     *
     * @param WorkerMessageReceivedEvent $event
     */
    public function onWorkerMessageReceived(WorkerMessageReceivedEvent $event)
    {
        $message = $event->getEnvelope()->getMessage();
        if (!$message instanceof Message) {
            return;
        }

        $consumed = 1;
        $nbItem = (is_countable($message->getPayload())) ? count($message->getPayload()) : 0;

        $consumerStat = new ConsumerStat($consumed, $nbItem);
        $this->storage->sendConsumerStat($consumerStat);

        if (null !== $this->logger) {
            $this->logger->info('WorkerMessageReceivedEvent - Consumer stats sent');
        }
    }

    /**
     * {@inheritDoc}
     * @codeCoverageIgnore
     */
    public static function getSubscribedEvents()
    {
        return [
            WorkerMessageReceivedEvent::class => 'onWorkerMessageReceived',
        ];
    }
}
