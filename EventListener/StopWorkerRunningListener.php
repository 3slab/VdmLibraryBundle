<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\EventListener;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\Event\WorkerRunningEvent;
use Vdm\Bundle\LibraryBundle\Service\StopWorkerService;

class StopWorkerRunningListener implements EventSubscriberInterface
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
     * StopWorkerRunningListener constructor.
     *
     * @param StopWorkerService $stopWorker
     * @param LoggerInterface|null $messengerLogger
     */
    public function __construct(StopWorkerService $stopWorker, LoggerInterface $messengerLogger = null)
    {
        $this->logger = $messengerLogger;
        $this->stopWorker = $stopWorker;
    }

    /**
     * Method executed on onWorkerRunning event
     *
     * @param WorkerRunningEvent $event
     */
    public function onWorkerRunning(WorkerRunningEvent $event)
    {
        $this->logger->debug('Check stop flag to true');
        if ($this->stopWorker->getFlag()) {
            $event->getWorker()->stop();
            $this->logger->debug('Worker stopped because of true stop flag');
        }
    }

    /**
     * {@inheritDoc}
     * @codeCoverageIgnore
     */
    public static function getSubscribedEvents()
    {
        return [
            WorkerRunningEvent::class => 'onWorkerRunning',
        ];
    }
}
