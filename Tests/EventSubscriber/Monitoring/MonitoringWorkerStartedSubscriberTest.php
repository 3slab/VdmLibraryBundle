<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Tests\EventSubscriber\Monitoring;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Event\WorkerStartedEvent;
use Symfony\Component\Messenger\Worker;
use Vdm\Bundle\LibraryBundle\EventSubscriber\Monitoring\MonitoringWorkerStartedSubscriber;
use Vdm\Bundle\LibraryBundle\Service\Monitoring\Monitoring;
use Vdm\Bundle\LibraryBundle\Service\Monitoring\MonitoringService;

class MonitoringWorkerStartedSubscriberTest extends TestCase
{
    public function testWorkerStartedEventSendMetric()
    {
        $storage = $this->createMock(MonitoringService::class);
        $storage->expects($this->once())
            ->method('update')
            ->with(Monitoring::RUNNING_STAT, 1);

        $worker = $this->createMock(Worker::class);

        $event = new WorkerStartedEvent($worker);

        $subscriber = new MonitoringWorkerStartedSubscriber($storage);
        $subscriber->onWorkerStartedEvent($event);
    }
}
