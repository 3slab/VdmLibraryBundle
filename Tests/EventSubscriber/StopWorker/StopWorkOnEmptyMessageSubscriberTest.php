<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Tests\EventSubscriber\StopWorker;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Event\WorkerMessageFailedEvent;
use Symfony\Component\Messenger\Event\WorkerMessageHandledEvent;
use Symfony\Component\Messenger\Event\WorkerMessageReceivedEvent;
use Vdm\Bundle\LibraryBundle\EventSubscriber\StopWorker\StopWorkOnEmptyMessageSubscriber;
use Vdm\Bundle\LibraryBundle\Service\StopWorkerService;
use Vdm\Bundle\LibraryBundle\Tests\Fixtures\AppBundle\Message\DefaultMessage;
use Vdm\Bundle\LibraryBundle\Tests\Fixtures\AppBundle\Message\NotIsEmptyMessage;

class StopWorkOnEmptyMessageSubscriberTest extends TestCase
{
    public function testOnWorkerMessageFailedEventWithoutIsEmptyMessage()
    {
        $stopWorker = new StopWorkerService();

        $message = new NotIsEmptyMessage();
        $envelope = new Envelope($message);

        $event = new WorkerMessageFailedEvent($envelope, 'collect', new \Exception());

        $subscriber = new StopWorkOnEmptyMessageSubscriber($stopWorker);
        $subscriber->onWorkerMessageFailedEvent($event);

        $this->assertFalse($message->isEmptyCalled);
        $this->assertFalse($stopWorker->getFlag());
    }

    public function testOnWorkerMessageFailedEventWithPayloadMessage()
    {
        $stopWorker = new StopWorkerService();

        $message = new DefaultMessage('not_empty');
        $envelope = new Envelope($message);

        $event = new WorkerMessageFailedEvent($envelope, 'collect', new \Exception());

        $subscriber = new StopWorkOnEmptyMessageSubscriber($stopWorker);
        $subscriber->onWorkerMessageFailedEvent($event);

        $this->assertFalse($stopWorker->getFlag());
    }

    public function testOnWorkerMessageFailedEventWithoutPayloadMessage()
    {
        $stopWorker = new StopWorkerService();

        $message = new DefaultMessage();
        $envelope = new Envelope($message);

        $event = new WorkerMessageFailedEvent($envelope, 'collect', new \Exception());

        $subscriber = new StopWorkOnEmptyMessageSubscriber($stopWorker);
        $subscriber->onWorkerMessageFailedEvent($event);

        $this->assertTrue($stopWorker->getFlag());
    }

    public function testOnWorkerMessageHandledEventWithoutIsEmptyMessage()
    {
        $stopWorker = new StopWorkerService();

        $message = new NotIsEmptyMessage();
        $envelope = new Envelope($message);

        $event = new WorkerMessageHandledEvent($envelope, 'collect');

        $subscriber = new StopWorkOnEmptyMessageSubscriber($stopWorker);
        $subscriber->onWorkerMessageHandledEvent($event);

        $this->assertFalse($message->isEmptyCalled);
        $this->assertFalse($stopWorker->getFlag());
    }

    public function testOnWorkerMessageHandledEventWithPayloadMessage()
    {
        $stopWorker = new StopWorkerService();

        $message = new DefaultMessage('not_empty');
        $envelope = new Envelope($message);

        $event = new WorkerMessageHandledEvent($envelope, 'collect');

        $subscriber = new StopWorkOnEmptyMessageSubscriber($stopWorker);
        $subscriber->onWorkerMessageHandledEvent($event);

        $this->assertFalse($stopWorker->getFlag());
    }

    public function testOnWorkerMessageHandledEventWithoutPayloadMessage()
    {
        $stopWorker = new StopWorkerService();

        $message = new DefaultMessage();
        $envelope = new Envelope($message);

        $event = new WorkerMessageHandledEvent($envelope, 'collect');

        $subscriber = new StopWorkOnEmptyMessageSubscriber($stopWorker);
        $subscriber->onWorkerMessageHandledEvent($event);

        $this->assertTrue($stopWorker->getFlag());
    }

    public function testOnWorkerMessageReceivedEventWithoutIsEmptyMessage()
    {
        $stopWorker = new StopWorkerService();

        $message = new NotIsEmptyMessage();
        $envelope = new Envelope($message);

        $event = new WorkerMessageReceivedEvent($envelope, 'collect');

        $subscriber = new StopWorkOnEmptyMessageSubscriber($stopWorker);
        $subscriber->onWorkerMessageReceivedEvent($event);

        $this->assertFalse($message->isEmptyCalled);
        $this->assertFalse($stopWorker->getFlag());
        $this->assertTrue($event->shouldHandle());
    }

    public function testOnWorkerMessageReceivedEventWithPayloadMessage()
    {
        $stopWorker = new StopWorkerService();

        $message = new DefaultMessage('not_empty');
        $envelope = new Envelope($message);

        $event = new WorkerMessageReceivedEvent($envelope, 'collect');

        $subscriber = new StopWorkOnEmptyMessageSubscriber($stopWorker);
        $subscriber->onWorkerMessageReceivedEvent($event);

        $this->assertFalse($stopWorker->getFlag());
        $this->assertTrue($event->shouldHandle());
    }

    public function testOnWorkerMessageReceivedEventWithoutPayloadMessage()
    {
        $stopWorker = new StopWorkerService();

        $message = new DefaultMessage();
        $envelope = new Envelope($message);

        $event = new WorkerMessageReceivedEvent($envelope, 'collect');

        $subscriber = new StopWorkOnEmptyMessageSubscriber($stopWorker);
        $subscriber->onWorkerMessageReceivedEvent($event);

        $this->assertTrue($stopWorker->getFlag());
        $this->assertFalse($event->shouldHandle());
    }
}
