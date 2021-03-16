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
use Symfony\Component\Messenger\Event\WorkerMessageFailedEvent;
use Vdm\Bundle\LibraryBundle\EventSubscriber\StopWorkerMessageFailedListener;
use Vdm\Bundle\LibraryBundle\Model\Message;
use Vdm\Bundle\LibraryBundle\Service\StopWorkerService;
use Vdm\Bundle\LibraryBundle\Stamp\StopAfterHandleStamp;

class StopWorkerMessageFailedListenerTest extends TestCase
{
    public function testOnWorkerMessageFailedEvent()
    {
        $service = new StopWorkerService();
        $envelope = new Envelope(new \stdClass());
        $error = $this->getMockBuilder(\Throwable::class)->getMock();
        $event = new WorkerMessageFailedEvent($envelope, '', $error);

        $listener = new StopWorkerMessageFailedListener($service, new NullLogger());
        $result = $listener->onWorkerMessageFailedEvent($event);

        $this->assertNull($result);       
    }

    /**
     * @dataProvider dataProviderTestOnWorkerMessageFailedEventStop
     */
    public function testOnWorkerMessageFailedEventStop($methodCall, $envelopeValue, $stamps)
    {
        $service = $this->getMockBuilder(StopWorkerService::class)->setMethods(['setFlag'])->getMock();
        $envelope = new Envelope($envelopeValue, $stamps);
        $error = $this->getMockBuilder(\Throwable::class)->getMock();
        $event = new WorkerMessageFailedEvent($envelope, '', $error);

        $service->expects($methodCall)->method('setFlag')->with(true);

        $listener = new StopWorkerMessageFailedListener($service, new NullLogger());
        $listener->onWorkerMessageFailedEvent($event);        
    }

    public function dataProviderTestOnWorkerMessageFailedEventStop()
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
