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
use Symfony\Component\Messenger\Event\WorkerRunningEvent;
use Vdm\Bundle\LibraryBundle\Service\StopWorkerService;

/**
 * Class StopWorkerOnExceptionSubscriber
 *
 * @package Vdm\Bundle\LibraryBundle\EventSubscriber\ExceptionHandler
 */
class StopWorkerOnExceptionSubscriber implements EventSubscriberInterface
{
    /**
     * @var LoggerInterface|null
     */
    private $logger;

    /**
     * @var bool
     */
    private $stopOnError;

    /**
     * @var StopWorkerService $stopWorker
     */
    private $stopWorker;

    /**
     * StopWorkerOnExceptionSubscriber constructor.
     *
     * @param StopWorkerService $stopWorker
     * @param bool $stopOnError
     * @param LoggerInterface|null $vdmLogger
     */
    public function __construct(StopWorkerService $stopWorker, bool $stopOnError = true, LoggerInterface $vdmLogger = null)
    {
        $this->stopWorker = $stopWorker;
        $this->stopOnError = $stopOnError;
        $this->logger = $vdmLogger ?? new NullLogger();
    }

    /**
     * Method executed on WorkerRunningEvent event
     *
     * @param WorkerRunningEvent $event
     */
    public function onWorkerRunningEvent(WorkerRunningEvent $event): void
    {
        if ($this->stopOnError && $this->stopWorker->getThrowable()) {
            $event->getWorker()->stop();
            $this->logger->debug('Exception thrown during message handling so worker is stopping');
        }
    }

    /**
     * {@inheritDoc}
     * @codeCoverageIgnore
     */
    public static function getSubscribedEvents(): array
    {
        return [
            WorkerRunningEvent::class => 'onWorkerRunningEvent',
        ];
    }
}
