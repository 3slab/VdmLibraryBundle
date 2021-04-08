<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Tests\EventSubscriber\ExceptionHandler;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Event\WorkerMessageFailedEvent;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\Stamp\SentToFailureTransportStamp;
use Vdm\Bundle\LibraryBundle\Event\CollectWorkerMessageFailedEvent;
use Vdm\Bundle\LibraryBundle\EventSubscriber\ExceptionHandler\StoreExceptionOnMessageFailedSubscriber;
use Vdm\Bundle\LibraryBundle\Service\StopWorkerService;
use Vdm\Bundle\LibraryBundle\Tests\Fixtures\AppBundle\Message\DefaultMessage;

class StoreExceptionOnMessageFailedSubscriberTest extends TestCase
{
    public function testExceptionNotStoredIfRetryStrategyOnWorkerMessageFailedEvent()
    {
        $stopWorker = new StopWorkerService();

        $message = new DefaultMessage();
        $envelope = new Envelope($message);

        $exception = new \Exception('error');

        $event = new WorkerMessageFailedEvent($envelope, 'transport', $exception);
        $event->setForRetry();

        $subscriber = new StoreExceptionOnMessageFailedSubscriber($stopWorker);
        $subscriber->onWorkerMessageFailedEvent($event);

        $this->assertNull($stopWorker->getThrowable());
    }

    public function testExceptionNotStoredIfTransportFailureStrategyOnWorkerMessageFailedEvent()
    {
        $stopWorker = new StopWorkerService();

        $message = new DefaultMessage();
        $envelope = new Envelope($message, [new SentToFailureTransportStamp('receiver')]);

        $exception = new \Exception('error');

        $event = new WorkerMessageFailedEvent($envelope, 'transport', $exception);

        $subscriber = new StoreExceptionOnMessageFailedSubscriber($stopWorker);
        $subscriber->onWorkerMessageFailedEvent($event);

        $this->assertNull($stopWorker->getThrowable());
    }

    public function testExceptionStoredOnWorkerMessageFailedEvent()
    {
        $stopWorker = new StopWorkerService();

        $message = new DefaultMessage();
        $envelope = new Envelope($message);

        $exception = new \Exception('error');

        $event = new WorkerMessageFailedEvent($envelope, 'transport', $exception);

        $subscriber = new StoreExceptionOnMessageFailedSubscriber($stopWorker);
        $subscriber->onWorkerMessageFailedEvent($event);

        $this->assertEquals($exception, $stopWorker->getThrowable());
    }

    public function testNestedExceptionStoredForHandlerFailedExceptionOnWorkerMessageFailedEvent()
    {
        $stopWorker = new StopWorkerService();

        $message = new DefaultMessage();
        $envelope = new Envelope($message);

        $nestedException = new \Exception('error');
        $exception = new HandlerFailedException($envelope, [$nestedException]);

        $event = new WorkerMessageFailedEvent($envelope, 'transport', $exception);

        $subscriber = new StoreExceptionOnMessageFailedSubscriber($stopWorker);
        $subscriber->onWorkerMessageFailedEvent($event);

        $this->assertEquals($nestedException, $stopWorker->getThrowable());
    }

    public function testExceptionNotStoredIfRetryStrategyOnCollectWorkerMessageFailedEvent()
    {
        $stopWorker = new StopWorkerService();

        $message = new DefaultMessage();
        $envelope = new Envelope($message);

        $exception = new \Exception('error');

        $event = new CollectWorkerMessageFailedEvent($envelope, 'transport', $exception);
        $event->setForRetry();

        $subscriber = new StoreExceptionOnMessageFailedSubscriber($stopWorker);
        $subscriber->onCollectWorkerMessageFailedEvent($event);

        $this->assertNull($stopWorker->getThrowable());
    }

    public function testExceptionNotStoredIfTransportFailureStrategyOnCollectWorkerMessageFailedEvent()
    {
        $stopWorker = new StopWorkerService();

        $message = new DefaultMessage();
        $envelope = new Envelope($message, [new SentToFailureTransportStamp('receiver')]);

        $exception = new \Exception('error');

        $event = new CollectWorkerMessageFailedEvent($envelope, 'transport', $exception);

        $subscriber = new StoreExceptionOnMessageFailedSubscriber($stopWorker);
        $subscriber->onCollectWorkerMessageFailedEvent($event);

        $this->assertNull($stopWorker->getThrowable());
    }

    public function testExceptionStoredOnCollectWorkerMessageFailedEvent()
    {
        $stopWorker = new StopWorkerService();

        $message = new DefaultMessage();
        $envelope = new Envelope($message);

        $exception = new \Exception('error');

        $event = new CollectWorkerMessageFailedEvent($envelope, 'transport', $exception);

        $subscriber = new StoreExceptionOnMessageFailedSubscriber($stopWorker);
        $subscriber->onCollectWorkerMessageFailedEvent($event);

        $this->assertEquals($exception, $stopWorker->getThrowable());
    }

    public function testNestedExceptionStoredForHandlerFailedExceptionOnCollectWorkerMessageFailedEvent()
    {
        $stopWorker = new StopWorkerService();

        $message = new DefaultMessage();
        $envelope = new Envelope($message);

        $nestedException = new \Exception('error');
        $exception = new HandlerFailedException($envelope, [$nestedException]);

        $event = new CollectWorkerMessageFailedEvent($envelope, 'transport', $exception);

        $subscriber = new StoreExceptionOnMessageFailedSubscriber($stopWorker);
        $subscriber->onCollectWorkerMessageFailedEvent($event);

        $this->assertEquals($nestedException, $stopWorker->getThrowable());
    }
}
