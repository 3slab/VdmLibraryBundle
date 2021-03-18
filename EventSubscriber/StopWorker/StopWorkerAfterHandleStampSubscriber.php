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
use Vdm\Bundle\LibraryBundle\Service\StopWorkerService;
use Vdm\Bundle\LibraryBundle\Stamp\StopAfterHandleStamp;

/**
 * Class StopWorkerAfterHandleStampSubscriber
 *
 * @package Vdm\Bundle\LibraryBundle\EventSubscriber\StopWorker
 */
class StopWorkerAfterHandleStampSubscriber implements EventSubscriberInterface
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
     * StopWorkerAfterHandleStampSubscriber constructor.
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
        $this->hasStopAfterHandlerStamp($event, 'WorkerMessageFailedEvent');
    }

    /**
     * Method executed on WorkerMessageHandledEvent event
     *
     * @param WorkerMessageHandledEvent $event
     */
    public function onWorkerMessageHandledEvent(WorkerMessageHandledEvent $event)
    {
        $this->hasStopAfterHandlerStamp($event, 'WorkerMessageHandledEvent');
    }

    /**
     * @param AbstractWorkerMessageEvent $event
     * @param string $eventName
     */
    protected function hasStopAfterHandlerStamp(AbstractWorkerMessageEvent $event, string $eventName)
    {
        $stamps = $event->getEnvelope()->all();
        if (in_array(StopAfterHandleStamp::class, array_keys($stamps))) {
            $this->logger->debug(
                'StopAfterHandleStamp detected on envelop during event {eventName} so we schedule to stop the worker',
                ['eventName' => $eventName]
            );
            $this->stopWorker->setFlag(true);
        }
    }

    /**
     * {@inheritDoc}
     * @codeCoverageIgnore
     */
    public static function getSubscribedEvents(): array
    {
        return [
            WorkerMessageFailedEvent::class => 'onWorkerMessageFailedEvent',
            WorkerMessageHandledEvent::class => 'onWorkerMessageHandledEvent',
        ];
    }
}