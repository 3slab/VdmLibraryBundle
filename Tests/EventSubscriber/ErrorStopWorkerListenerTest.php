<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Tests\EventSubscriber;

use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Symfony\Component\Messenger\Event\WorkerRunningEvent;
use Vdm\Bundle\LibraryBundle\EventSubscriber\ErrorDuringMessageHandlerListener;
use Vdm\Bundle\LibraryBundle\EventSubscriber\ErrorStopWorkerListener;

class ErrorStopWorkerListenerTest extends TestCase
{
    public function testOnWorkerRunning()
    {
        $worker = $this
                    ->getMockBuilder(\Symfony\Component\Messenger\Worker::class)
                    ->disableOriginalConstructor()
                    ->setMethods(['stop'])
                    ->getMock();
        $worker->expects($this->once())->method('stop');

        $event = new WorkerRunningEvent($worker, true);
        $errorListener = $this->getMockBuilder(ErrorDuringMessageHandlerListener::class)->getMock();
        $errorListener->method('getThrownException')->willReturn(new \Exception(''));

        $listener = new ErrorStopWorkerListener($errorListener, new NullLogger());
        $listener->onWorkerRunning($event);
    }

    public function testOnWorkerRunningThrowable()
    {
        $worker = $this->getMockBuilder(\Symfony\Component\Messenger\Worker::class)->disableOriginalConstructor()->getMock();
        $event = new WorkerRunningEvent($worker, true);
        $errorListener = new ErrorDuringMessageHandlerListener();

        $listener = new ErrorStopWorkerListener($errorListener, new NullLogger());
        $return = $listener->onWorkerRunning($event);

        $this->assertNull($return);
    }
}
