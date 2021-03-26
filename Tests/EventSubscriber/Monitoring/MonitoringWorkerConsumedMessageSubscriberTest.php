<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Tests\EventSubscriber\Monitoring;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Event\WorkerMessageReceivedEvent;
use Vdm\Bundle\LibraryBundle\EventSubscriber\Monitoring\MonitoringWorkerConsumedMessageSubscriber;
use Vdm\Bundle\LibraryBundle\Service\Monitoring\Monitoring;
use Vdm\Bundle\LibraryBundle\Service\Monitoring\MonitoringService;
use Vdm\Bundle\LibraryBundle\Tests\Fixtures\AppBundle\Message\DefaultMessage;

class MonitoringWorkerConsumedMessageSubscriberTest extends TestCase
{
    public function testWorkerConsumedMessageSendMetric()
    {
        $message = new DefaultMessage();
        $envelope = new Envelope($message);

        $storage = $this->createMock(MonitoringService::class);
        $storage->expects($this->once())
            ->method('increment')
            ->with(Monitoring::CONSUMED_STAT, 1);

        $event = new WorkerMessageReceivedEvent($envelope, 'collect');

        $subscriber = new MonitoringWorkerConsumedMessageSubscriber($storage);
        $subscriber->onWorkerMessageReceivedEvent($event);
    }
}
