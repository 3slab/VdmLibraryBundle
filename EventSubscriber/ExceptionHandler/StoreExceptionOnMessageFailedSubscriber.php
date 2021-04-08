<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\EventSubscriber\ExceptionHandler;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\Event\WorkerMessageFailedEvent;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\Stamp\SentToFailureTransportStamp;
use Vdm\Bundle\LibraryBundle\Event\CollectWorkerMessageFailedEvent;
use Vdm\Bundle\LibraryBundle\Service\StopWorkerService;

/**
 * Class StoreExceptionOnMessageFailedSubscriber
 *
 * @package Vdm\Bundle\LibraryBundle\EventSubscriber\ExceptionHandler
 */
class StoreExceptionOnMessageFailedSubscriber implements EventSubscriberInterface
{
    /**
     * @var StopWorkerService $stopWorker
     */
    private $stopWorker;

    /**
     * @var LoggerInterface|null
     */
    private $logger;

    /**
     * StoreExceptionOnMessageFailedSubscriber constructor.
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
        $this->storeException($event);
    }

    /**
     * Method executed on CollectWorkerMessageFailedEvent event
     *
     * @param CollectWorkerMessageFailedEvent $event
     */
    public function onCollectWorkerMessageFailedEvent(CollectWorkerMessageFailedEvent $event)
    {
        $this->storeException($event);
    }

    /**
     * @param WorkerMessageFailedEvent|CollectWorkerMessageFailedEvent $event
     */
    public function storeException($event)
    {
        // Retry strategy kicked in
        if ($event->willRetry()) {
            return;
        }

        $envelope = $event->getEnvelope();

        // Transport failure strategy kicked in
        if (null !== $envelope->last(SentToFailureTransportStamp::class)) {
            return;
        }

        $throwable = $event->getThrowable();
        if ($throwable instanceof HandlerFailedException) {
            $throwable = $throwable->getNestedExceptions()[0];
        }

        $this->stopWorker->setThrowable($throwable);

        $message = $envelope->getMessage();
        if (is_object($message)) {
            $this->logger->info('An exception {exceptionClass} occurred during handling of {class} message', [
                'exceptionClass' => \get_class($throwable),
                'class' => \get_class($message)
            ]);
        }
    }

    /**
     * {@inheritDoc}
     * @codeCoverageIgnore
     */
    public static function getSubscribedEvents(): array
    {
        return [
            // Should be executed after all listener related to retry or failed transport strategy have run
            CollectWorkerMessageFailedEvent::class => ['onCollectWorkerMessageFailedEvent', -200],
            WorkerMessageFailedEvent::class => ['onWorkerMessageFailedEvent', -200],
        ];
    }
}
