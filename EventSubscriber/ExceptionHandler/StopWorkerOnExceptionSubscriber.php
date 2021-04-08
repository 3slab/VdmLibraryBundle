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
use Symfony\Component\Messenger\Worker;
use Vdm\Bundle\LibraryBundle\Event\CollectWorkerRunningEvent;
use Vdm\Bundle\LibraryBundle\Service\CollectWorker;
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
        $this->checkStopWorkerThrowable($event->getWorker());
    }

    /**
     * Method executed on CollectWorkerRunningEvent event
     *
     * @param CollectWorkerRunningEvent $event
     */
    public function onCollectWorkerRunningEvent(CollectWorkerRunningEvent $event): void
    {
        $this->checkStopWorkerThrowable($event->getWorker());
    }

    /**
     * @param Worker|CollectWorker $worker
     */
    protected function checkStopWorkerThrowable($worker)
    {
        if ($this->stopOnError && $this->stopWorker->getThrowable()) {
            $worker->stop();
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
            CollectWorkerRunningEvent::class => 'onCollectWorkerRunningEvent',
            WorkerRunningEvent::class => 'onWorkerRunningEvent',
        ];
    }
}
