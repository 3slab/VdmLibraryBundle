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
use Symfony\Component\Messenger\Event\WorkerRunningEvent;
use Symfony\Component\Messenger\Worker;
use Vdm\Bundle\LibraryBundle\Event\CollectWorkerRunningEvent;
use Vdm\Bundle\LibraryBundle\Service\CollectWorker;
use Vdm\Bundle\LibraryBundle\Service\StopWorkerService;

/**
 * Class StopWorkerCheckFlagPresenceSubscriber
 *
 * @package Vdm\Bundle\LibraryBundle\EventSubscriber\StopWorker
 */
class StopWorkerCheckFlagPresenceSubscriber implements EventSubscriberInterface
{
    /**
     * @var StopWorkerService $stopWorker
     */
    private $stopWorker;

    /**
     * @var bool
     */
    private $stopOnError;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * StopWorkerCheckFlagPresenceSubscriber constructor.
     *
     * @param StopWorkerService $stopWorker
     * @param bool $stopOnError
     * @param LoggerInterface|null $vdmLogger
     */
    public function __construct(
        StopWorkerService $stopWorker,
        bool $stopOnError = true,
        LoggerInterface $vdmLogger = null
    ) {
        $this->stopWorker = $stopWorker;
        $this->stopOnError = $stopOnError;
        $this->logger = $vdmLogger ?? new NullLogger();
    }

    /**
     * Method executed on onWorkerRunning event
     *
     * @param WorkerRunningEvent $event
     */
    public function onWorkerRunningEvent(WorkerRunningEvent $event)
    {
        $this->checkStopWorkerFlag($event->getWorker());
    }

    /**
     * Method executed on onCollectWorkerRunning event
     *
     * @param CollectWorkerRunningEvent $event
     */
    public function onCollectWorkerRunningEvent(CollectWorkerRunningEvent $event)
    {
        $this->checkStopWorkerFlag($event->getWorker());
    }

    /**
     * @param Worker|CollectWorker $worker
     */
    protected function checkStopWorkerFlag($worker)
    {
        if ($this->stopOnError && $this->stopWorker->getFlag()) {
            $worker->stop();
            $this->logger->debug('Stop flag presence detected during WorkerRunningEvent event so worker is stopping');
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
