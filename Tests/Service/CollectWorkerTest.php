<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Tests\Service;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\RejectRedeliveredMessageException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Transport\Receiver\ReceiverInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Vdm\Bundle\LibraryBundle\Event\CollectWorkerMessageFailedEvent;
use Vdm\Bundle\LibraryBundle\Event\CollectWorkerMessageHandledEvent;
use Vdm\Bundle\LibraryBundle\Event\CollectWorkerMessageReceivedEvent;
use Vdm\Bundle\LibraryBundle\Event\CollectWorkerRunningEvent;
use Vdm\Bundle\LibraryBundle\Event\CollectWorkerStartedEvent;
use Vdm\Bundle\LibraryBundle\Event\CollectWorkerStoppedEvent;
use Vdm\Bundle\LibraryBundle\Service\CollectWorker;
use Vdm\Bundle\LibraryBundle\Tests\Fixtures\AppBundle\Message\DefaultMessage;

/**
 * Class CollectWorkerTest
 * @package Vdm\Bundle\LibraryBundle\Tests\Service
 */
class CollectWorkerTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|MessageBusInterface
     */
    protected $bus;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * @var array
     */
    protected $receivers;

    /**
     * {@inheritDoc}
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->bus = $this->createMock(MessageBusInterface::class);
        $this->dispatcher = $this->createMock(EventDispatcherInterface::class);

        $receiver = $this->createMock(ReceiverInterface::class);
        $this->receivers = [$receiver];
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown(): void
    {
        parent::tearDown();
        $this->bus = null;
        $this->dispatcher = null;
        $this->receivers = null;
    }

    public function testEventDispatchedWhenNoMessageHandled()
    {
        $this->dispatcher
            ->expects($this->exactly(2))
            ->method('dispatch')
            ->withConsecutive(
                [$this->isInstanceOf(CollectWorkerStartedEvent::class)],
                [$this->isInstanceOf(CollectWorkerStoppedEvent::class)]
            );

        $this->receivers[0]
            ->expects($this->once())
            ->method('get')
            ->willReturn([]);

        $worker = new CollectWorker($this->receivers, $this->bus, $this->dispatcher);
        $worker->run();
    }

    public function testEventDispatchedWhenMessageHandled()
    {
        $this->dispatcher
            ->expects($this->exactly(8))
            ->method('dispatch')
            ->withConsecutive(
                [$this->isInstanceOf(CollectWorkerStartedEvent::class)],
                [$this->isInstanceOf(CollectWorkerMessageReceivedEvent::class)],
                [$this->isInstanceOf(CollectWorkerMessageHandledEvent::class)],
                [$this->isInstanceOf(CollectWorkerRunningEvent::class)],
                [$this->isInstanceOf(CollectWorkerMessageReceivedEvent::class)],
                [$this->isInstanceOf(CollectWorkerMessageHandledEvent::class)],
                [$this->isInstanceOf(CollectWorkerRunningEvent::class)],
                [$this->isInstanceOf(CollectWorkerStoppedEvent::class)]
            );

        $message1 = new DefaultMessage(['key1' => 'value1']);
        $envelope1 = new Envelope($message1);

        $message2 = new DefaultMessage(['key2' => 'value2']);
        $envelope2 = new Envelope($message2);

        $this->receivers[0]
            ->expects($this->once())
            ->method('get')
            ->willReturn([$envelope1, $envelope2]);

        $this->receivers[0]
            ->expects($this->exactly(2))
            ->method('ack');

        $this->bus
            ->expects($this->exactly(2))
            ->method('dispatch')
            ->willReturnOnConsecutiveCalls(
                $envelope1,
                $envelope2
            );

        $worker = new CollectWorker($this->receivers, $this->bus, $this->dispatcher);
        $worker->run();
    }

    public function testEventDispatchedWhenMessageFailed()
    {
        $this->dispatcher
            ->expects($this->exactly(5))
            ->method('dispatch')
            ->withConsecutive(
                [$this->isInstanceOf(CollectWorkerStartedEvent::class)],
                [$this->isInstanceOf(CollectWorkerMessageReceivedEvent::class)],
                [$this->isInstanceOf(CollectWorkerMessageFailedEvent::class)],
                [$this->isInstanceOf(CollectWorkerRunningEvent::class)],
                [$this->isInstanceOf(CollectWorkerStoppedEvent::class)]
            );

        $message1 = new DefaultMessage(['key1' => 'value1']);
        $envelope1 = new Envelope($message1);

        $this->receivers[0]
            ->expects($this->once())
            ->method('get')
            ->willReturn([$envelope1]);

        $this->receivers[0]
            ->expects($this->never())
            ->method('ack');

        $this->receivers[0]
            ->expects($this->once())
            ->method('reject');

        $this->bus
            ->expects($this->exactly(1))
            ->method('dispatch')
            ->willReturnCallback(function ($envelope) {
                throw new \Exception('trigger error');
            });

        $worker = new CollectWorker($this->receivers, $this->bus, $this->dispatcher);
        $worker->run();
    }

    public function testEventDispatchedWhenMessageFailedRejectFirst()
    {
        $this->dispatcher
            ->expects($this->exactly(5))
            ->method('dispatch')
            ->withConsecutive(
                [$this->isInstanceOf(CollectWorkerStartedEvent::class)],
                [$this->isInstanceOf(CollectWorkerMessageReceivedEvent::class)],
                [$this->isInstanceOf(CollectWorkerMessageFailedEvent::class)],
                [$this->isInstanceOf(CollectWorkerRunningEvent::class)],
                [$this->isInstanceOf(CollectWorkerStoppedEvent::class)]
            );

        $message1 = new DefaultMessage(['key1' => 'value1']);
        $envelope1 = new Envelope($message1);

        $this->receivers[0]
            ->expects($this->once())
            ->method('get')
            ->willReturn([$envelope1]);

        $this->receivers[0]
            ->expects($this->never())
            ->method('ack');

        $this->receivers[0]
            ->expects($this->once())
            ->method('reject');

        $this->bus
            ->expects($this->exactly(1))
            ->method('dispatch')
            ->willReturnCallback(function ($envelope) {
                throw new RejectRedeliveredMessageException('trigger error');
            });

        $worker = new CollectWorker($this->receivers, $this->bus, $this->dispatcher);
        $worker->run();
    }

    public function testEventDispatchedWhenShouldNotHandle()
    {
        $this->dispatcher
            ->expects($this->exactly(4))
            ->method('dispatch')
            ->willReturnCallback(function ($event) {
                if ($event instanceof CollectWorkerMessageReceivedEvent) {
                    $event->shouldHandle(false);
                }
                return new \stdClass();
            })
            ->withConsecutive(
                [$this->isInstanceOf(CollectWorkerStartedEvent::class)],
                [$this->isInstanceOf(CollectWorkerMessageReceivedEvent::class)],
                [$this->isInstanceOf(CollectWorkerRunningEvent::class)],
                [$this->isInstanceOf(CollectWorkerStoppedEvent::class)]
            );

        $message1 = new DefaultMessage(['key1' => 'value1']);
        $envelope1 = new Envelope($message1);

        $this->receivers[0]
            ->expects($this->once())
            ->method('get')
            ->willReturn([$envelope1]);

        $this->receivers[0]
            ->expects($this->never())
            ->method('ack');

        $this->bus
            ->expects($this->never())
            ->method('dispatch');

        $worker = new CollectWorker($this->receivers, $this->bus, $this->dispatcher);
        $worker->run();
    }

    public function testEventDispatchedWhenShouldStop()
    {
        $receiver = $this->createMock(ReceiverInterface::class);
        $this->receivers[] = $receiver;

        $this->dispatcher
            ->expects($this->exactly(5))
            ->method('dispatch')
            ->willReturnCallback(function ($event) {
                if ($event instanceof CollectWorkerRunningEvent) {
                    $event->getWorker()->stop();
                }
                return new \stdClass();
            })
            ->withConsecutive(
                [$this->isInstanceOf(CollectWorkerStartedEvent::class)],
                [$this->isInstanceOf(CollectWorkerMessageReceivedEvent::class)],
                [$this->isInstanceOf(CollectWorkerMessageHandledEvent::class)],
                [$this->isInstanceOf(CollectWorkerRunningEvent::class)],
                [$this->isInstanceOf(CollectWorkerStoppedEvent::class)]
            );

        $message1 = new DefaultMessage(['key1' => 'value1']);
        $envelope1 = new Envelope($message1);

        $message2 = new DefaultMessage(['key2' => 'value2']);
        $envelope2 = new Envelope($message2);

        $this->receivers[0]
            ->expects($this->once())
            ->method('get')
            ->willReturn([$envelope1, $envelope2]);

        $this->receivers[1]
            ->expects($this->never())
            ->method('get');

        $this->receivers[0]
            ->expects($this->exactly(1))
            ->method('ack');

        $this->bus
            ->expects($this->exactly(1))
            ->method('dispatch')
            ->willReturn($envelope1);

        $worker = new CollectWorker($this->receivers, $this->bus, $this->dispatcher);
        $worker->run();
    }
}
