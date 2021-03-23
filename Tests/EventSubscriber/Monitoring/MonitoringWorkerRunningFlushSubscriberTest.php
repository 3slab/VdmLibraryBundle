<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Tests\EventSubscriber\Monitoring;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Event\WorkerRunningEvent;
use Symfony\Component\Messenger\Worker;
use Vdm\Bundle\LibraryBundle\EventSubscriber\Monitoring\MonitoringWorkerRunningFlushSubscriber;
use Vdm\Bundle\LibraryBundle\Service\Monitoring\MonitoringService;

class MonitoringWorkerRunningFlushSubscriberTest extends TestCase
{
    public function testWorkerRunningFlushMetric()
    {
        $storage = $this->createMock(MonitoringService::class);
        $storage->expects($this->once())
            ->method('flush')
            ->with();

        $worker = $this->createMock(Worker::class);

        $event = new WorkerRunningEvent($worker, false);

        $subscriber = new MonitoringWorkerRunningFlushSubscriber($storage);
        $subscriber->onWorkerRunningEvent($event);
    }
}
