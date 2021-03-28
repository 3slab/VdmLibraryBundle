<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\EventSubscriber\Monitoring;

use Psr\Log\NullLogger;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Vdm\Bundle\LibraryBundle\Monitoring\Model\RunningStat;
use Vdm\Bundle\LibraryBundle\Service\Monitoring\Monitoring;
use Vdm\Bundle\LibraryBundle\Service\Monitoring\MonitoringService;
use Vdm\Bundle\LibraryBundle\Service\Monitoring\Storage\StorageInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\Event\WorkerStartedEvent;

/**
 * Class MonitoringWorkerTerminateSubscriber
 *
 * @package Vdm\Bundle\LibraryBundle\EventSubscriber\Monitoring
 */
class MonitoringWorkerTerminateSubscriber implements EventSubscriberInterface
{
    /**
     * @var MonitoringService
     */
    protected $monitoring;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * MonitoringWorkerTerminateSubscriber constructor.
     *
     * @param MonitoringService $monitoring
     * @param LoggerInterface|null $vdmLogger
     */
    public function __construct(MonitoringService $monitoring, LoggerInterface $vdmLogger = null)
    {
        $this->monitoring = $monitoring;
        $this->logger = $vdmLogger ?? new NullLogger();
    }

    /**
     * Method executed on ConsoleTerminateEvent event
     *
     * @param ConsoleTerminateEvent $event
     */
    public function onConsoleTerminateEvent(ConsoleTerminateEvent $event)
    {
        $this->monitoring->update(Monitoring::RUNNING_STAT, 0);
        $this->logger->debug('worker stopped metric sent');

        $this->monitoring->flush();
        $this->logger->debug('metric storage flushed');
    }

    /**
     * {@inheritDoc}
     * @codeCoverageIgnore
     */
    public static function getSubscribedEvents(): array
    {
        return [
            ConsoleTerminateEvent::class => 'onConsoleTerminateEvent',
        ];
    }
}
