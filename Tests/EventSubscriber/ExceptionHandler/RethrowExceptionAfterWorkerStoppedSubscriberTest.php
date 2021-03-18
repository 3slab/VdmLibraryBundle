<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 *
 * @noinspection PhpVoidFunctionResultUsedInspection
 */

namespace Vdm\Bundle\LibraryBundle\Tests\EventSubscriber\ExceptionHandler;

use Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Event\WorkerStoppedEvent;
use Symfony\Component\Messenger\Exception\RuntimeException;
use Symfony\Component\Messenger\Worker;
use Vdm\Bundle\LibraryBundle\EventSubscriber\ExceptionHandler\RethrowExceptionAfterWorkerStoppedSubscriber;
use Vdm\Bundle\LibraryBundle\Service\StopWorkerService;

class RethrowExceptionAfterWorkerStoppedSubscriberTest extends TestCase
{
    public function testExceptionNotThrownIfNotCaughtDuringMessageHandling()
    {
        $stopWorker = new StopWorkerService();

        $worker = $this->createMock(Worker::class);
        $event = new WorkerStoppedEvent($worker);

        $subscriber = new RethrowExceptionAfterWorkerStoppedSubscriber($stopWorker);
        $result = $subscriber->onWorkerStoppedEvent($event);

        $this->assertNull($result);
    }

    public function testWorkerNotStoppingIfExceptionCaught()
    {
        $this->expectException(RuntimeException::class);

        $stopWorker = new StopWorkerService();
        $exception = new Exception('error');
        $stopWorker->setThrowable($exception);

        $worker = $this->createMock(Worker::class);
        $event = new WorkerStoppedEvent($worker);

        $subscriber = new RethrowExceptionAfterWorkerStoppedSubscriber($stopWorker);
        $subscriber->onWorkerStoppedEvent($event);
    }
}
