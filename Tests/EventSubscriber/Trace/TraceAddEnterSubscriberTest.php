<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Tests\EventSubscriber\Trace;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Event\WorkerMessageReceivedEvent;
use Vdm\Bundle\LibraryBundle\EventSubscriber\Trace\TraceAddEnterSubscriber;
use Vdm\Bundle\LibraryBundle\Model\Trace;
use Vdm\Bundle\LibraryBundle\Tests\Message\DefaultMessage;
use Vdm\Bundle\LibraryBundle\Tests\Message\NotTraceableMessage;

class TraceAddEnterSubscriberTest extends TestCase
{
    public function testNoTraceAddedIfNotTraceableMessage()
    {
        $message = new NotTraceableMessage();
        $envelope = new Envelope($message);
        $event = new WorkerMessageReceivedEvent($envelope, 'collect');

        $subscriber = new TraceAddEnterSubscriber('');
        $subscriber->onWorkerMessageReceivedEvent($event);

        $this->assertFalse($message->isAddTraceCalled);
    }

    public function testTraceAddedIfTraceableMessage()
    {
        $message = new DefaultMessage();
        $envelope = new Envelope($message);
        $event = new WorkerMessageReceivedEvent($envelope, 'collect');

        $subscriber = new TraceAddEnterSubscriber('myapp');
        $subscriber->onWorkerMessageReceivedEvent($event);

        $traces = $message->getTraces();
        $this->assertCount(1, $traces);
        $this->assertEquals('myapp', $traces[0]->getName());
        $this->assertEquals(Trace::ENTER, $traces[0]->getEvent());
    }

    public function testTraceAddedIfTraceableMessageWithPreviousTraces()
    {
        $message = new DefaultMessage();
        $message->addTrace(new Trace('trace1', Trace::ENTER));
        $message->addTrace(new Trace('trace2', Trace::EXIT));
        $envelope = new Envelope($message);
        $event = new WorkerMessageReceivedEvent($envelope, 'collect');

        $subscriber = new TraceAddEnterSubscriber('myapp');
        $subscriber->onWorkerMessageReceivedEvent($event);

        $traces = $message->getTraces();
        $this->assertCount(3, $traces);
        $this->assertEquals('myapp', $traces[2]->getName());
        $this->assertEquals(Trace::ENTER, $traces[2]->getEvent());
    }
}
