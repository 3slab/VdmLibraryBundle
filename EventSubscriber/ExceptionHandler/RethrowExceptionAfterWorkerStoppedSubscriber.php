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
use Symfony\Component\Messenger\Event\WorkerStoppedEvent;
use Symfony\Component\Messenger\Exception\RuntimeException;
use Vdm\Bundle\LibraryBundle\Service\StopWorkerService;

/**
 * Class RethrowExceptionAfterWorkerStoppedSubscriber
 *
 * @package Vdm\Bundle\LibraryBundle\EventSubscriber\ExceptionHandler
 */
class RethrowExceptionAfterWorkerStoppedSubscriber implements EventSubscriberInterface
{
    /**
     * @var LoggerInterface|null
     */
    private $logger;

    /**
     * @var StopWorkerService $stopWorker
     */
    private $stopWorker;

    /**
     * RethrowExceptionAfterWorkerStoppedSubscriber constructor.
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
     * Method executed on WorkerStoppedEvent event
     *
     * @param WorkerStoppedEvent $event
     */
    public function onWorkerStoppedEvent(WorkerStoppedEvent $event): void
    {
        $throwable = $this->stopWorker->getThrowable();
        if (!$throwable) {
            return;
        }

        $this->logger->info(
            'Worker stopped because an of an exception during message handling. Rethrowing exception ...'
        );

        // Rethrow caught exception in handler after worker stopped to exit cli with an error code different from 0
        throw new RuntimeException('Worker stopped because of handler exception', 1, $throwable);
    }

    /**
     * {@inheritDoc}
     * @codeCoverageIgnore
     */
    public static function getSubscribedEvents(): array
    {
        return [
            // Execute after monitoring tracking listener
            WorkerStoppedEvent::class => ['onWorkerStoppedEvent', -200],
        ];
    }
}
