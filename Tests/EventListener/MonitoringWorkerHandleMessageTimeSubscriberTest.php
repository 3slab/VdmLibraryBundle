<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Tests\EventListener;

use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Event\WorkerMessageFailedEvent;
use Symfony\Component\Messenger\Event\WorkerMessageHandledEvent;
use Symfony\Component\Messenger\Event\WorkerMessageReceivedEvent;
use Symfony\Component\Stopwatch\Stopwatch;
use Vdm\Bundle\LibraryBundle\EventListener\MonitoringWorkerHandleMessageTimeSubscriber;
use Vdm\Bundle\LibraryBundle\Model\Message;
use Vdm\Bundle\LibraryBundle\Monitoring\StatsStorageInterface;

class MonitoringWorkerHandleMessageTimeSubscriberTest extends TestCase
{
    public function testOnWorkerMessageReceived()
    {
        $envelope = new Envelope(new Message(''));
        $event = new WorkerMessageReceivedEvent($envelope, '');
        $statsStorageInterface = $this->getMockBuilder(StatsStorageInterface::class)->getMock();
        $listener = new MonitoringWorkerHandleMessageTimeSubscriber($statsStorageInterface, new NullLogger());
        $listener->onWorkerMessageReceived($event);
                
        $property = new \ReflectionProperty(MonitoringWorkerHandleMessageTimeSubscriber::class, 'stopwatch');
        $property->setAccessible(true);
        $value = $property->getValue($listener);

        $this->assertInstanceOf(Stopwatch::class, $value);
    }

    public function testOnWorkerMessageFailed()
    {
        $envelope = new Envelope(new Message(''));
        $eventReceived = new WorkerMessageReceivedEvent($envelope, '');
        $error = $this->getMockBuilder(\Throwable::class)->getMock();
        $event = new WorkerMessageFailedEvent($envelope, '', $error);

        $statsStorageInterface = $this->getMockBuilder(StatsStorageInterface::class)->getMock();
        $statsStorageInterface->expects($this->once())->method('sendTimeStat');
        $listener = new MonitoringWorkerHandleMessageTimeSubscriber($statsStorageInterface, new NullLogger());
        $listener->onWorkerMessageReceived($eventReceived);
        $listener->onWorkerMessageFailed($event);
    }

    public function testOnWorkerMessageHandled()
    {
        $envelope = new Envelope(new Message(''));
        $eventReceived = new WorkerMessageReceivedEvent($envelope, '');
        $event = new WorkerMessageHandledEvent($envelope, '');

        $statsStorageInterface = $this->getMockBuilder(StatsStorageInterface::class)->getMock();
        $statsStorageInterface->expects($this->once())->method('sendTimeStat');
        $listener = new MonitoringWorkerHandleMessageTimeSubscriber($statsStorageInterface, new NullLogger());
        $listener->onWorkerMessageReceived($eventReceived);
        $listener->onWorkerMessageHandled($event);
    }
}
