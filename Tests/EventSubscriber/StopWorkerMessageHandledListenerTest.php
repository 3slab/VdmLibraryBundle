<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Tests\EventSubscriber;

use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Event\WorkerMessageHandledEvent;
use Vdm\Bundle\LibraryBundle\EventSubscriber\StopWorkerMessageHandledListener;
use Vdm\Bundle\LibraryBundle\Model\Message;
use Vdm\Bundle\LibraryBundle\Service\StopWorkerService;
use Vdm\Bundle\LibraryBundle\Stamp\StopAfterHandleStamp;

class StopWorkerMessageHandledListenerTest extends TestCase
{
    public function testOnWorkerMessageHandledEvent()
    {
        $service = new StopWorkerService();
        $envelope = new Envelope(new \stdClass());
        $event = new WorkerMessageHandledEvent($envelope, '');

        $listener = new StopWorkerMessageHandledListener($service, new NullLogger());
        $result = $listener->onWorkerMessageHandledEvent($event);

        $this->assertNull($result);       
    }

    /**
     * @dataProvider dataProviderTestOnWorkerMessageHandledEventStop
     */
    public function testOnWorkerMessageHandledEventStop($methodCall, $envelopeValue, $stamps)
    {
        $service = $this->getMockBuilder(StopWorkerService::class)->setMethods(['setFlag'])->getMock();
        $envelope = new Envelope($envelopeValue, $stamps);
        $event = new WorkerMessageHandledEvent($envelope, '');

        $service->expects($methodCall)->method('setFlag')->with(true);

        $listener = new StopWorkerMessageHandledListener($service, new NullLogger());
        $listener->onWorkerMessageHandledEvent($event);        
    }

    public function dataProviderTestOnWorkerMessageHandledEventStop()
    {
        yield [
            $this->never(),
            new \stdClass(),
            []
        ];
        yield [
            $this->once(),
            new Message('test'),
            [new StopAfterHandleStamp()]
        ];
        yield [
            $this->once(),
            new Message(''),
            []
        ];
        yield [
            $this->never(),
            new Message('test'),
            []
        ];
    }
}
