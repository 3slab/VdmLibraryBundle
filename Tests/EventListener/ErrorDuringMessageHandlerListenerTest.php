<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Tests\EventListener;

use Psr\Log\NullLogger;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Event\WorkerMessageFailedEvent;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\Stamp\SentToFailureTransportStamp;
use Vdm\Bundle\LibraryBundle\EventListener\ErrorDuringMessageHandlerListener;
use Vdm\Bundle\LibraryBundle\Model\Message;

class ErrorDuringMessageHandlerListenerTest extends TestCase
{
    public function testOnMessageFailedWillRetry()
    {
        $listener = new ErrorDuringMessageHandlerListener(new NullLogger());

        $envelope = new Envelope(new Message(''));
        $error = $this->getMockBuilder(HandlerFailedException::class)->disableOriginalConstructor()->getMock();
        $event = new WorkerMessageFailedEvent($envelope, '', $error);
        $event->setForRetry();

        $return = $listener->onMessageFailed($event);

        $this->assertNull($return);
    }

    public function testOnMessageFailedTransportFailure()
    {
        $listener = new ErrorDuringMessageHandlerListener(new NullLogger());

        $envelope = new Envelope(new Message(''), [new SentToFailureTransportStamp('')]);
        $error = $this->getMockBuilder(HandlerFailedException::class)->disableOriginalConstructor()->getMock();
        $event = new WorkerMessageFailedEvent($envelope, '', $error);

        $return = $listener->onMessageFailed($event);

        $this->assertNull($return);
    }

    public function testOnMessageFailedThrowable()
    {
        $logger = $this->getMockBuilder(LoggerInterface::class)->getMock();
        $listener = new ErrorDuringMessageHandlerListener($logger);

        $envelope = new Envelope(new Message(''));
        $exceptionSend = new \Exception('');
        $error = $this->getMockBuilder(HandlerFailedException::class)->disableOriginalConstructor()->setMethods(['getNestedExceptions'])->getMock();
        $error->method('getNestedExceptions')->willReturn([$exceptionSend]);
        $event = new WorkerMessageFailedEvent($envelope, '', $error);

        $logger->expects($this->once())->method('info');

        $listener->onMessageFailed($event);

        $this->assertSame($exceptionSend, $listener->getThrownException());
    }
}
