<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Tests\EventSubscriber\StopWorker;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Event\WorkerRunningEvent;
use Symfony\Component\Messenger\Worker;
use Vdm\Bundle\LibraryBundle\EventSubscriber\StopWorker\StopWorkerCheckFlagPresenceSubscriber;
use Vdm\Bundle\LibraryBundle\Service\StopWorkerService;

class StopWorkerCheckFlagPresenceSubscriberTest extends TestCase
{
    public function testWorkerNotStoppingIfFlagNotSet()
    {
        $stopWorker = new StopWorkerService();

        $worker = $this->createMock(Worker::class);
        $worker->expects($this->never())
            ->method('stop');

        $event = new WorkerRunningEvent($worker, false);

        $subscriber = new StopWorkerCheckFlagPresenceSubscriber($stopWorker);
        $subscriber->onWorkerRunningEvent($event);
    }

    public function testWorkerStoppingIfFlagIsSet()
    {
        $stopWorker = new StopWorkerService();
        $stopWorker->setFlag(true);

        $worker = $this->createMock(Worker::class);
        $worker->expects($this->once())
            ->method('stop');

        $event = new WorkerRunningEvent($worker, false);

        $subscriber = new StopWorkerCheckFlagPresenceSubscriber($stopWorker);
        $subscriber->onWorkerRunningEvent($event);
    }

    public function testWorkerNotStoppingIfFlagIsSetButFeatureIsDisabled()
    {
        $stopWorker = new StopWorkerService();
        $stopWorker->setFlag(true);

        $worker = $this->createMock(Worker::class);
        $worker->expects($this->never())
            ->method('stop');

        $event = new WorkerRunningEvent($worker, false);

        $subscriber = new StopWorkerCheckFlagPresenceSubscriber($stopWorker, false);
        $subscriber->onWorkerRunningEvent($event);
    }
}
