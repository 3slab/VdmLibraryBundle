<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Tests\EventSubscriber\Monitoring;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Event\WorkerMessageHandledEvent;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Vdm\Bundle\LibraryBundle\EventSubscriber\Monitoring\MonitoringWorkerHandledMessageSubscriber;
use Vdm\Bundle\LibraryBundle\Service\Monitoring\Monitoring;
use Vdm\Bundle\LibraryBundle\Service\Monitoring\MonitoringService;
use Vdm\Bundle\LibraryBundle\Tests\Fixtures\AppBundle\Message\DefaultMessage;

class MonitoringWorkerHandledMessageSubscriberTest extends TestCase
{
    public function testWorkerHandledMessageSendMetric()
    {
        $message = new DefaultMessage();
        $envelope = new Envelope($message, [new HandledStamp(1, 'handler')]);

        $storage = $this->createMock(MonitoringService::class);
        $storage->expects($this->once())
            ->method('increment')
            ->with(Monitoring::HANDLED_STAT, 1);

        $event = new WorkerMessageHandledEvent($envelope, 'collect');

        $subscriber = new MonitoringWorkerHandledMessageSubscriber($storage);
        $subscriber->onWorkerMessageHandledEvent($event);
    }

    public function testWorkerHandledMessageMetricNotSentIfNotHandled()
    {
        $message = new DefaultMessage();
        $envelope = new Envelope($message);

        $storage = $this->createMock(MonitoringService::class);
        $storage->expects($this->never())
            ->method('increment');

        $event = new WorkerMessageHandledEvent($envelope, 'collect');

        $subscriber = new MonitoringWorkerHandledMessageSubscriber($storage);
        $subscriber->onWorkerMessageHandledEvent($event);
    }
}
