<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Tests\EventSubscriber;

use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Event\WorkerMessageReceivedEvent;
use Vdm\Bundle\LibraryBundle\EventSubscriber\TraceAddEnterListener;
use Vdm\Bundle\LibraryBundle\Model\Message;

class TraceAddEnterListenerTest extends TestCase
{
    public function testOnWorkerMessageReceived()
    {
        $envelope = new Envelope(new \stdClass());
        $event = new WorkerMessageReceivedEvent($envelope, '');

        $listener = new TraceAddEnterListener('', new NullLogger());
        $result = $listener->onWorkerMessageReceived($event);

        $this->assertNull($result);
    }

    /**
     * @dataProvider dataProviderTestOnWorkerMessageReceivedAdd
     */
    public function testOnWorkerMessageReceivedAdd($methodCall, $envelopeValue)
    {
        $envelope = new Envelope($envelopeValue);
        $event = new WorkerMessageReceivedEvent($envelope, '');
        
        $logger = $this->getMockBuilder(LoggerInterface::class)->getMock();
        $logger->expects($methodCall)->method('info');

        $listener = new TraceAddEnterListener('', $logger);
        $listener->onWorkerMessageReceived($event);
    }

    public function dataProviderTestOnWorkerMessageReceivedAdd()
    {
        yield [
            $this->never(),
            new \stdClass()
        ];
        yield [
            $this->never(),
            new Message('')
        ];
        yield [
            $this->once(),
            new Message('test'),
            []
        ];
    }
}
