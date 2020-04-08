<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Tests\EventListener;

use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Symfony\Component\Messenger\Event\WorkerStoppedEvent;
use Symfony\Component\Messenger\Exception\RuntimeException;
use Vdm\Bundle\LibraryBundle\EventListener\ErrorDuringMessageHandlerListener;
use Vdm\Bundle\LibraryBundle\EventListener\ErrorRethrowAfterWorkerStoppedListener;

class ErrorRethrowAfterWorkerStoppedListenerTest extends TestCase
{
    public function testOnWorkerStopped()
    {
        $worker = $this->getMockBuilder(\Symfony\Component\Messenger\Worker::class)->disableOriginalConstructor()->getMock();
        $event = new WorkerStoppedEvent($worker);
        $errorListener = $this->getMockBuilder(ErrorDuringMessageHandlerListener::class)->getMock();
        $errorListener->method('getThrownException')->willReturn(new \Exception(''));

        $this->expectException(RuntimeException::class);
        $listener = new ErrorRethrowAfterWorkerStoppedListener($errorListener, new NullLogger());
        $listener->onWorkerStopped($event);

        
    }

    public function testOnWorkerStoppedThrowable()
    {
        $worker = $this->getMockBuilder(\Symfony\Component\Messenger\Worker::class)->disableOriginalConstructor()->getMock();
        $event = new WorkerStoppedEvent($worker);
        $errorListener = new ErrorDuringMessageHandlerListener();

        $listener = new ErrorRethrowAfterWorkerStoppedListener($errorListener, new NullLogger());
        $return = $listener->onWorkerStopped($event);

        $this->assertNull($return);
    }
}
