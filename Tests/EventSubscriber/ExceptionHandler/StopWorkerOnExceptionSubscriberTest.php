<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Tests\EventSubscriber\ExceptionHandler;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Event\WorkerRunningEvent;
use Symfony\Component\Messenger\Worker;
use Vdm\Bundle\LibraryBundle\Event\CollectWorkerRunningEvent;
use Vdm\Bundle\LibraryBundle\EventSubscriber\StopWorker\StopWorkerCheckFlagPresenceSubscriber;
use Vdm\Bundle\LibraryBundle\Service\CollectWorker;
use Vdm\Bundle\LibraryBundle\Service\StopWorkerService;

/**
 * Class StopWorkerOnExceptionSubscriberTest
 *
 * @package Vdm\Bundle\LibraryBundle\Tests\EventSubscriber\ExceptionHandler
 */
class StopWorkerOnExceptionSubscriberTest extends TestCase
{
    public function testWorkerNotStoppingIfFlagNotSetOnWorkerRunningEvent()
    {
        $stopWorker = new StopWorkerService();

        $worker = $this->createMock(Worker::class);
        $worker->expects($this->never())
            ->method('stop');

        $event = new WorkerRunningEvent($worker, false);

        $subscriber = new StopWorkerCheckFlagPresenceSubscriber($stopWorker);
        $subscriber->onWorkerRunningEvent($event);
    }

    public function testWorkerStoppingIfFlagIsSetOnWorkerRunningEvent()
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

    public function testWorkerNotStoppingIfFlagIsSetButFeatureDisabledOnWorkerRunningEvent()
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

    public function testWorkerNotStoppingIfFlagNotSetOnCollectWorkerRunningEvent()
    {
        $stopWorker = new StopWorkerService();

        $worker = $this->createMock(CollectWorker::class);
        $worker->expects($this->never())
            ->method('stop');

        $event = new CollectWorkerRunningEvent($worker, false);

        $subscriber = new StopWorkerCheckFlagPresenceSubscriber($stopWorker);
        $subscriber->onCollectWorkerRunningEvent($event);
    }

    public function testWorkerStoppingIfFlagIsSetOnCollectWorkerRunningEvent()
    {
        $stopWorker = new StopWorkerService();
        $stopWorker->setFlag(true);

        $worker = $this->createMock(CollectWorker::class);
        $worker->expects($this->once())
            ->method('stop');

        $event = new CollectWorkerRunningEvent($worker, false);

        $subscriber = new StopWorkerCheckFlagPresenceSubscriber($stopWorker);
        $subscriber->onCollectWorkerRunningEvent($event);
    }

    public function testWorkerNotStoppingIfFlagIsSetButFeatureDisabledOnCollectWorkerRunningEvent()
    {
        $stopWorker = new StopWorkerService();
        $stopWorker->setFlag(true);

        $worker = $this->createMock(CollectWorker::class);
        $worker->expects($this->never())
            ->method('stop');

        $event = new CollectWorkerRunningEvent($worker, false);

        $subscriber = new StopWorkerCheckFlagPresenceSubscriber($stopWorker, false);
        $subscriber->onCollectWorkerRunningEvent($event);
    }
}
