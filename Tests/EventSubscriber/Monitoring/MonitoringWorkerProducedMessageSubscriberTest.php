<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Tests\EventSubscriber\Monitoring;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Event\SendMessageToTransportsEvent;
use Vdm\Bundle\LibraryBundle\EventSubscriber\Monitoring\MonitoringWorkerProducedMessageSubscriber;
use Vdm\Bundle\LibraryBundle\Service\Monitoring\Monitoring;
use Vdm\Bundle\LibraryBundle\Service\Monitoring\MonitoringService;
use Vdm\Bundle\LibraryBundle\Tests\Fixtures\AppBundle\Message\DefaultMessage;

class MonitoringWorkerProducedMessageSubscriberTest extends TestCase
{
    public function testWorkerProducedMessageSendMetric()
    {
        $message = new DefaultMessage();
        $envelope = new Envelope($message);

        $storage = $this->createMock(MonitoringService::class);
        $storage->expects($this->once())
            ->method('increment')
            ->with(Monitoring::PRODUCED_STAT, 1);

        $event = new SendMessageToTransportsEvent($envelope);

        $subscriber = new MonitoringWorkerProducedMessageSubscriber($storage);
        $subscriber->onSendMessageToTransportEvent($event);
    }
}
