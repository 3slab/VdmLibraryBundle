<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Tests\EventSubscriber\Trace;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Event\SendMessageToTransportsEvent;
use Vdm\Bundle\LibraryBundle\EventSubscriber\Trace\TraceAddExitSubscriber;
use Vdm\Bundle\LibraryBundle\Model\Trace;
use Vdm\Bundle\LibraryBundle\Tests\Message\DefaultMessage;
use Vdm\Bundle\LibraryBundle\Tests\Message\NotTraceableMessage;

class TraceAddExitSubscriberTest extends TestCase
{
    public function testNoTraceAddedIfNotTraceableMessage()
    {
        $message = new NotTraceableMessage();
        $envelope = new Envelope($message);
        $event = new SendMessageToTransportsEvent($envelope, '');

        $subscriber = new TraceAddExitSubscriber('');
        $subscriber->onSendMessageToTransportEvent($event);

        $this->assertFalse($message->isAddTraceCalled);
    }

    public function testTraceAddedIfTraceableMessageWithoutPreviousTraces()
    {
        $message = new DefaultMessage();
        $envelope = new Envelope($message);
        $event = new SendMessageToTransportsEvent($envelope);

        $subscriber = new TraceAddExitSubscriber('myapp');
        $subscriber->onSendMessageToTransportEvent($event);

        $traces = $message->getTraces();
        $this->assertCount(1, $traces);
        $this->assertEquals('myapp', $traces[0]->getName());
        $this->assertEquals(Trace::EXIT, $traces[0]->getEvent());
    }

    public function testTraceAddedIfTraceableMessageWithPreviousTraces()
    {
        $message = new DefaultMessage();
        $message->addTrace(new Trace('trace1', Trace::EXIT));
        $message->addTrace(new Trace('trace2', Trace::ENTER));

        $envelope = new Envelope($message);
        $event = new SendMessageToTransportsEvent($envelope);

        $subscriber = new TraceAddExitSubscriber('myapp');
        $subscriber->onSendMessageToTransportEvent($event);

        $traces = $message->getTraces();
        $this->assertCount(3, $traces);
        $this->assertEquals('trace2', $traces[2]->getName());
        $this->assertEquals(Trace::EXIT, $traces[2]->getEvent());
    }
}
