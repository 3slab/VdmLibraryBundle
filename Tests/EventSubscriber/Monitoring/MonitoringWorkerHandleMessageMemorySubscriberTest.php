<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Tests\EventSubscriber\Monitoring;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Event\WorkerMessageFailedEvent;
use Symfony\Component\Messenger\Event\WorkerMessageHandledEvent;
use Symfony\Component\Messenger\Event\WorkerMessageReceivedEvent;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Vdm\Bundle\LibraryBundle\EventSubscriber\Monitoring\MonitoringWorkerHandleMessageMemorySubscriber;
use Vdm\Bundle\LibraryBundle\Service\Monitoring\Monitoring;
use Vdm\Bundle\LibraryBundle\Service\Monitoring\MonitoringService;
use Vdm\Bundle\LibraryBundle\Tests\Message\DefaultMessage;

class MonitoringWorkerHandleMessageMemorySubscriberTest extends TestCase
{
    protected $envelope;

    protected $storage;

    public function setUp(): void
    {
        parent::setUp();

        $message = new DefaultMessage();
        $this->envelope = new Envelope($message, [new HandledStamp(1, 'handler')]);

        $this->storage = $this->createMock(MonitoringService::class);
        $this->storage->expects($this->once())
            ->method('update')
            ->will($this->returnCallback(function($key, $value) {
                $this->assertEquals(Monitoring::MEMORY_STAT, $key);
                $this->assertEquals(memory_get_usage(true), $value);
            }));
    }

    public function tearDown(): void
    {
        parent::tearDown();

        $this->envelope = null;
        $this->storage = null;
    }

    public function testWorkerMessageHandledSendMetric()
    {
        $event = new WorkerMessageHandledEvent($this->envelope, 'collect');

        $subscriber = new MonitoringWorkerHandleMessageMemorySubscriber($this->storage);
        $subscriber->onWorkerMessageReceivedEvent(new WorkerMessageReceivedEvent($this->envelope, 'collect'));
        $subscriber->onWorkerMessageHandledEvent($event);
    }

    public function testWorkerMessageFailedSendMetric()
    {
        $event = new WorkerMessageFailedEvent($this->envelope, 'collect', new \Exception('here'));

        $subscriber = new MonitoringWorkerHandleMessageMemorySubscriber($this->storage);
        $subscriber->onWorkerMessageReceivedEvent(new WorkerMessageReceivedEvent($this->envelope, 'collect'));
        $subscriber->onWorkerMessageFailedEvent($event);
    }
}
