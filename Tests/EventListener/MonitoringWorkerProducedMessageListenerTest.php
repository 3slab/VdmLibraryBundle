<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Tests\EventListener;

use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Event\SendMessageToTransportsEvent;
use Symfony\Component\Messenger\Stamp\SentStamp;
use Vdm\Bundle\LibraryBundle\EventListener\MonitoringWorkerProducedMessageListener;
use Vdm\Bundle\LibraryBundle\Model\Message;
use Vdm\Bundle\LibraryBundle\Monitoring\StatsStorageInterface;

class MonitoringWorkerProducedMessageListenerTest extends TestCase
{
    /**
     * @var StatsStorageInterface $statsStorageInterface
     */
    private $statsStorageInterface;

    protected function getSubscriber(array $calls)
    {
        $this->statsStorageInterface = $this->getMockBuilder(StatsStorageInterface::class)->getMock();
        $this->statsStorageInterface->expects($calls['sendProducedStat'])->method('sendProducedStat');

        return new MonitoringWorkerProducedMessageListener($this->statsStorageInterface, new NullLogger());
    }

    /**
     * @dataProvider dataProviderTestOnWorkerMessageProduced
     */
    public function testOnWorkerMessageProduced($methodCall, $envelopeValue, $stamps)
    {
        $listener = $this->getSubscriber(['sendProducedStat' => $methodCall]);

        $envelope = new Envelope($envelopeValue, $stamps);
        $event = new SendMessageToTransportsEvent($envelope);

        $listener->onWorkerMessageProduced($event);
    }

    public function dataProviderTestOnWorkerMessageProduced()
    {
        yield [
            $this->never(),
            new \stdClass(),
            []
        ];
        yield [
            $this->once(),
            new Message(''),
            []
        ];
    }
}
