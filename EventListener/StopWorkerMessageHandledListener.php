<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\EventListener;

use Vdm\Bundle\LibraryBundle\Model\Message;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\Event\WorkerMessageHandledEvent;
use Vdm\Bundle\LibraryBundle\Service\StopWorkerService;
use Vdm\Bundle\LibraryBundle\Stamp\StopAfterHandleStamp;

class StopWorkerMessageHandledListener implements EventSubscriberInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var StopWorkerService $stopWorker
     */
    private $stopWorker;

    /**
     * StopWorkerMessageHandledListener constructor.
     *
     * @param StatsStorageInterface $storage
     * @param LoggerInterface|null $messengerLogger
     */
    public function __construct(StopWorkerService $stopWorker, LoggerInterface $messengerLogger = null)
    {
        $this->logger = $messengerLogger;
        $this->stopWorker = $stopWorker;
    }

    /**
     * Method executed on WorkerMessageHandledEvent event
     *
     * @param WorkerMessageHandledEvent $event
     */
    public function onWorkerMessageHandledEvent(WorkerMessageHandledEvent $event)
    {
        $message = $event->getEnvelope()->getMessage();

        $stamps = $event->getEnvelope()->all();
        if (in_array(StopAfterHandleStamp::class, array_keys($stamps))) {
            $this->logger->debug('WorkerMessageHandledEvent - StopAfterHandleStamp detected so we stop the worker');
            $this->stopWorker->setFlag(true);
        }

        if (!$message instanceof Message) {
            return;
        }

        $payload = $message->getPayload();
        if (empty($payload)) {
            $this->logger->debug('WorkerMessageHandledEvent - Empty payload detected so we stop worker');
            $this->stopWorker->setFlag(true);
        }
        
    }

    /**
     * {@inheritDoc}
     * @codeCoverageIgnore
     */
    public static function getSubscribedEvents()
    {
        return [
            WorkerMessageHandledEvent::class => 'onWorkerMessageHandledEvent',
        ];
    }
}
