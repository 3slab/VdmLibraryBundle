<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\EventSubscriber;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\Event\WorkerMessageFailedEvent;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\Stamp\SentToFailureTransportStamp;

class ErrorDuringMessageHandlerListener implements EventSubscriberInterface
{
    /**
     * @var LoggerInterface|null
     */
    private $logger;

    /**
     * @var \Exception
     */
    private $throwable;

    /**
     * ErrorDuringMessageHandlerListener constructor.
     *
     * @param LoggerInterface|null $vdmLogger
     */
    public function __construct(LoggerInterface $vdmLogger = null)
    {
        $this->logger = $vdmLogger ?? new NullLogger();
    }

    /**
     * Method executed on WorkerMessageFailedEvent event
     *
     * @param WorkerMessageFailedEvent $event
     */
    public function onMessageFailed(WorkerMessageFailedEvent $event)
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

        // Keep trace of exception because current instance injected in other listeners which need to know this
        $this->throwable = $throwable;
        
        $this->logger->info('WorkerMessageFailedEvent - An exception occurred during handling of {class} message', [
            'class' => \get_class($envelope->getMessage())
        ]);
    }

    /**
     * @return \Exception
     */
    public function getThrownException()
    {
        return $this->throwable;
    }

    /**
     * {@inheritDoc}
     * @codeCoverageIgnore
     */
    public static function getSubscribedEvents()
    {
        return [
            // Should be executed after all listener related to retry or failed transport strategy have run
            WorkerMessageFailedEvent::class => ['onMessageFailed', -200],
        ];
    }
}
