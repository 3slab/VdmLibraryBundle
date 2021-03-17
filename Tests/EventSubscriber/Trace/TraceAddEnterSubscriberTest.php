<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Tests\EventSubscriber;

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

        $listener = new TraceAddEnterSubscriber('');
        $listener->onWorkerMessageReceived($event);

        $this->assertFalse($message->isAddTraceCalled);
    }

    public function testTraceAddedIfTraceableMessage()
    {
        $message = new DefaultMessage();
        $envelope = new Envelope($message);
        $event = new WorkerMessageReceivedEvent($envelope, 'collect');

        $listener = new TraceAddEnterSubscriber('myapp');
        $listener->onWorkerMessageReceived($event);

        $traces = $message->getTraces();
        $this->assertCount(1, $traces);
        $this->assertEquals('myapp-collect', $traces[0]->getName());
        $this->assertEquals(Trace::ENTER, $traces[0]->getEvent());
    }
}
