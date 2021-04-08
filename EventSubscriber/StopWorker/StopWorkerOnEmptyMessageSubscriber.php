<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\EventSubscriber\StopWorker;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\Event\AbstractWorkerMessageEvent;
use Symfony\Component\Messenger\Event\WorkerMessageFailedEvent;
use Symfony\Component\Messenger\Event\WorkerMessageHandledEvent;
use Symfony\Component\Messenger\Event\WorkerMessageReceivedEvent;
use Vdm\Bundle\LibraryBundle\Event\CollectWorkerMessageFailedEvent;
use Vdm\Bundle\LibraryBundle\Event\CollectWorkerMessageHandledEvent;
use Vdm\Bundle\LibraryBundle\Event\CollectWorkerMessageReceivedEvent;
use Vdm\Bundle\LibraryBundle\Model\IsEmptyMessageInterface;
use Vdm\Bundle\LibraryBundle\Model\Message;
use Vdm\Bundle\LibraryBundle\Service\StopWorkerService;

/**
 * Class StopWorkerOnEmptyMessageSubscriber
 *
 * @package Vdm\Bundle\LibraryBundle\EventSubscriber\StopWorker
 */
class StopWorkerOnEmptyMessageSubscriber implements EventSubscriberInterface
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
     * StopWorkOnEmptyMessageSubscriber constructor.
     *
     * @param StopWorkerService $stopWorker
     * @param LoggerInterface|null $vdmLogger
     */
    public function __construct(StopWorkerService $stopWorker, LoggerInterface $vdmLogger = null)
    {
        $this->stopWorker = $stopWorker;
        $this->logger = $vdmLogger ?? new NullLogger();
    }

    /**
     * Method executed on WorkerMessageFailedEvent event
     *
     * @param WorkerMessageFailedEvent $event
     */
    public function onWorkerMessageFailedEvent(WorkerMessageFailedEvent $event)
    {
        $this->isEmptyMessage($event, 'WorkerMessageFailedEvent');
    }

    /**
     * Method executed on WorkerMessageHandledEvent event
     *
     * @param WorkerMessageHandledEvent $event
     */
    public function onWorkerMessageHandledEvent(WorkerMessageHandledEvent $event)
    {
        $this->isEmptyMessage($event, 'WorkerMessageReceivedEvent');
    }

    /**
     * Method executed on WorkerMessageReceivedEvent event
     *
     * @param WorkerMessageReceivedEvent $event
     */
    public function onWorkerMessageReceivedEvent(WorkerMessageReceivedEvent $event)
    {
        $this->isEmptyMessage($event, 'WorkerMessageReceivedEvent');
    }

    /**
     * Method executed on CollectWorkerMessageFailedEvent event
     *
     * @param CollectWorkerMessageFailedEvent $event
     */
    public function onCollectWorkerMessageFailedEvent(CollectWorkerMessageFailedEvent $event)
    {
        $this->isEmptyMessage($event, 'CollectWorkerMessageFailedEvent');
    }

    /**
     * Method executed on CollectWorkerMessageHandledEvent event
     *
     * @param CollectWorkerMessageHandledEvent $event
     */
    public function onCollectWorkerMessageHandledEvent(CollectWorkerMessageHandledEvent $event)
    {
        $this->isEmptyMessage($event, 'CollectWorkerMessageReceivedEvent');
    }

    /**
     * Method executed on CollectWorkerMessageReceivedEvent event
     *
     * @param CollectWorkerMessageReceivedEvent $event
     */
    public function onCollectWorkerMessageReceivedEvent(CollectWorkerMessageReceivedEvent $event)
    {
        $this->isEmptyMessage($event, 'CollectWorkerMessageReceivedEvent');
    }

    /**
     * @param AbstractWorkerMessageEvent $event
     * @param string $eventName
     */
    protected function isEmptyMessage(AbstractWorkerMessageEvent $event, string $eventName)
    {
        $message = $event->getEnvelope()->getMessage();
        if (!$message instanceof IsEmptyMessageInterface) {
            return;
        }

        if (!$message->isEmpty()) {
            return;
        }

        $this->logger->debug(
            'Empty message detected during event {eventName} so we schedule to stop the worker',
            ['eventName' => $eventName]
        );
        $this->stopWorker->setFlag(true);

        if ($event instanceof WorkerMessageReceivedEvent || $event instanceof CollectWorkerMessageReceivedEvent) {
            $event->shouldHandle(false);
            $this->logger->debug(
                'Set ShouldHandle flag on {eventName} event to false as the message is empty',
                ['eventName' => $eventName]
            );
        }
    }

    /**
     * {@inheritDoc}
     * @codeCoverageIgnore
     */
    public static function getSubscribedEvents(): array
    {
        return [
            CollectWorkerMessageFailedEvent::class => 'onCollectWorkerMessageFailedEvent',
            CollectWorkerMessageHandledEvent::class => 'onCollectWorkerMessageHandledEvent',
            CollectWorkerMessageReceivedEvent::class => 'onCollectWorkerMessageReceivedEvent',
            WorkerMessageFailedEvent::class => 'onWorkerMessageFailedEvent',
            WorkerMessageHandledEvent::class => 'onWorkerMessageHandledEvent',
            WorkerMessageReceivedEvent::class => 'onWorkerMessageReceivedEvent',
        ];
    }
}