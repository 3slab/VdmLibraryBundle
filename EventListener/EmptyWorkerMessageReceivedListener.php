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
use Symfony\Component\Messenger\Event\WorkerMessageReceivedEvent;
use Vdm\Bundle\LibraryBundle\Service\StopWorkerService;

class EmptyWorkerMessageReceivedListener implements EventSubscriberInterface
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
     * EmptyWorkerMessageReceivedListener constructor.
     *
     * @param StopWorkerService $stopWorker
     * @param LoggerInterface|null $messengerLogger
     */
    public function __construct(StopWorkerService $stopWorker, LoggerInterface $messengerLogger = null)
    {
        $this->stopWorker = $stopWorker;
        $this->logger = $messengerLogger;
    }

    /**
     * Method executed on WorkerMessageReceivedEvent event
     *
     * @param WorkerMessageReceivedEvent $event
     */
    public function onWorkerMessageReceivedEvent(WorkerMessageReceivedEvent $event)
    {
        $message = $event->getEnvelope()->getMessage();
        if (!$message instanceof Message) {
            return;
        }

        $this->logger->debug('WorkerMessageReceivedEvent - Check content not empty');
        $payload = $message->getPayload();
        if (empty($payload)) {
            $this->stopWorker->setFlag(true);
            $event->shouldHandle(false);
            $this->logger->debug('WorkerMessageReceivedEvent - Do not treat the message because of empty');
        }
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            WorkerMessageReceivedEvent::class => 'onWorkerMessageReceivedEvent',
        ];
    }
}
