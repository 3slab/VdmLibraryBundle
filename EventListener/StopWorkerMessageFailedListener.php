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
use Symfony\Component\Messenger\Event\WorkerMessageFailedEvent;
use Vdm\Bundle\LibraryBundle\Service\StopWorkerService;
use Vdm\Bundle\LibraryBundle\Stamp\StopAfterHandleStamp;

class StopWorkerMessageFailedListener implements EventSubscriberInterface
{
    /**
     * @var StopWorkerService $stopWorker
     */
    private $stopWorker;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * StopWorkerMessageFailedListener constructor.
     *
     * @param StopWorkerService $stopWorker
     * @param LoggerInterface|null $vdmLogger
     */
    public function __construct(StopWorkerService $stopWorker, LoggerInterface $vdmLogger = null)
    {
        $this->logger = $vdmLogger;
        $this->stopWorker = $stopWorker;
    }

    /**
     * Method executed on WorkerMessageFailedEvent event
     *
     * @param WorkerMessageFailedEvent $event
     */
    public function onWorkerMessageFailedEvent(WorkerMessageFailedEvent $event)
    {
        $message = $event->getEnvelope()->getMessage();
        
        if (!$message instanceof Message) {
            return;
        }

        $stamps = $event->getEnvelope()->all();
        if (in_array(StopAfterHandleStamp::class, array_keys($stamps))) {
            $this->logger->debug('WorkerMessageHandledEvent - StopAfterHandleStamp detected so whe stop the worker');
            $this->stopWorker->setFlag(true);
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
            WorkerMessageFailedEvent::class => 'onWorkerMessageFailedEvent',
        ];
    }
}
