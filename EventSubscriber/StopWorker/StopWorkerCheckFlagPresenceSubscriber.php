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
     * @var LoggerInterface
     */
    private $logger;

    /**
     * StopWorkerMessageFailedListener constructor.
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
     * Method executed on onWorkerRunning event
     *
     * @param WorkerRunningEvent $event
     */
    public function onWorkerRunning(WorkerRunningEvent $event)
    {
        if ($this->stopWorker->getFlag()) {
            $event->getWorker()->stop();
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
            WorkerRunningEvent::class => 'onWorkerRunning',
        ];
    }
}