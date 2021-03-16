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
use Symfony\Component\Messenger\Event\WorkerMessageReceivedEvent;
use Vdm\Bundle\LibraryBundle\EventSubscriber\EmptyWorkerMessageReceivedListener;
use Vdm\Bundle\LibraryBundle\Model\Message;
use Vdm\Bundle\LibraryBundle\Service\StopWorkerService;

class EmptyWorkerMessageReceivedListenerTest extends TestCase
{
    /**
     * @dataProvider dataProviderTestOnWorkerMessageReceivedEvent
     */
    public function testOnWorkerMessageReceivedEvent($methodCall, $envelopeValue)
    {
        $stopWorkerService = $this->getMockBuilder(StopWorkerService::class)->setMethods(['setFlag'])->getMock();
        $emptyWorker = new EmptyWorkerMessageReceivedListener($stopWorkerService, new NullLogger());
        $envelope = new Envelope($envelopeValue);

        $event = new WorkerMessageReceivedEvent($envelope, '');

        $stopWorkerService->expects($methodCall)->method('setFlag')->with(true);

        $emptyWorker->onWorkerMessageReceivedEvent($event);
    }


    public function dataProviderTestOnWorkerMessageReceivedEvent()
    {
        yield [
            $this->never(),
            new \stdClass()
        ];
        yield [
            $this->once(),
            new Message('')
        ];
    }
}
