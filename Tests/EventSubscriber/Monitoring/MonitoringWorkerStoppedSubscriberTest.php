<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Tests\EventSubscriber\Monitoring;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\Event\WorkerStoppedEvent;
use Symfony\Component\Messenger\Worker;
use Vdm\Bundle\LibraryBundle\Event\CollectWorkerStoppedEvent;
use Vdm\Bundle\LibraryBundle\EventSubscriber\Monitoring\MonitoringWorkerStoppedSubscriber;
use Vdm\Bundle\LibraryBundle\Service\CollectWorker;
use Vdm\Bundle\LibraryBundle\Service\Monitoring\Monitoring;
use Vdm\Bundle\LibraryBundle\Service\Monitoring\MonitoringService;

class MonitoringWorkerStoppedSubscriberTest extends TestCase
{
    public function testWorkerStoppedEventSentMetricAndFlush()
    {
        $storage = $this->createMock(MonitoringService::class);

        $storage->expects($this->once())
            ->method('update')
            ->with(Monitoring::RUNNING_STAT, 0);

        $storage->expects($this->once())
            ->method('flush')
            ->with();

        $event = new WorkerStoppedEvent($this->createMock(Worker::class));

        $subscriber = new MonitoringWorkerStoppedSubscriber($storage);
        $subscriber->onWorkerStoppedEvent($event);
    }

    public function testCollectWorkerStoppedEventSentMetricAndFlush()
    {
        $storage = $this->createMock(MonitoringService::class);

        $storage->expects($this->once())
            ->method('update')
            ->with(Monitoring::RUNNING_STAT, 0);

        $storage->expects($this->once())
            ->method('flush')
            ->with();

        $event = new CollectWorkerStoppedEvent($this->createMock(CollectWorker::class));

        $subscriber = new MonitoringWorkerStoppedSubscriber($storage);
        $subscriber->onCollectWorkerStoppedEvent($event);
    }
}
