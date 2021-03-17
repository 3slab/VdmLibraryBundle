<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Tests\EventSubscriber;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Event\WorkerMessageFailedEvent;
use Symfony\Component\Messenger\Event\WorkerMessageHandledEvent;
use Vdm\Bundle\LibraryBundle\EventSubscriber\StopWorker\StopWorkerAfterHandleStampSubscriber;
use Vdm\Bundle\LibraryBundle\Service\StopWorkerService;
use Vdm\Bundle\LibraryBundle\Stamp\StopAfterHandleStamp;
use Vdm\Bundle\LibraryBundle\Tests\Message\DefaultMessage;

class StopWorkerAfterHandleStampSubscriberTest extends TestCase
{
    public function testOnWorkerMessageHandledEventWithoutStopAfterHandleStamp()
    {
        $stopService = new StopWorkerService();

        $message = new DefaultMessage();
        $envelope = new Envelope($message);
        $event = new WorkerMessageHandledEvent($envelope, 'collect');

        $listener = new StopWorkerAfterHandleStampSubscriber($stopService);
        $listener->onWorkerMessageHandledEvent($event);

        $this->assertFalse($stopService->getFlag());
    }

    public function testOnWorkerMessageHandledEventWithStopAfterHandleStamp()
    {
        $stopService = new StopWorkerService();

        $message = new DefaultMessage();
        $envelope = new Envelope($message, [new StopAfterHandleStamp()]);
        $event = new WorkerMessageHandledEvent($envelope, 'collect');

        $listener = new StopWorkerAfterHandleStampSubscriber($stopService);
        $listener->onWorkerMessageHandledEvent($event);

        $this->assertTrue($stopService->getFlag());
    }

    public function testOnWorkerMessageFailedEventWithoutStopAfterHandleStamp()
    {
        $stopService = new StopWorkerService();

        $message = new DefaultMessage();
        $envelope = new Envelope($message);
        $event = new WorkerMessageFailedEvent($envelope, 'collect', new \Exception());

        $listener = new StopWorkerAfterHandleStampSubscriber($stopService);
        $listener->onWorkerMessageFailedEvent($event);

        $this->assertFalse($stopService->getFlag());
    }

    public function testOnWorkerMessageFailedEventWithStopAfterHandleStamp()
    {
        $stopService = new StopWorkerService();

        $message = new DefaultMessage();
        $envelope = new Envelope($message, [new StopAfterHandleStamp()]);
        $event = new WorkerMessageFailedEvent($envelope, 'collect', new \Exception());

        $listener = new StopWorkerAfterHandleStampSubscriber($stopService);
        $listener->onWorkerMessageFailedEvent($event);

        $this->assertTrue($stopService->getFlag());
    }
}