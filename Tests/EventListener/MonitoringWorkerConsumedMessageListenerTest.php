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
use Symfony\Component\Messenger\Event\WorkerMessageReceivedEvent;
use Vdm\Bundle\LibraryBundle\EventListener\MonitoringWorkerConsumedMessageListener;
use Vdm\Bundle\LibraryBundle\Model\Message;
use Vdm\Bundle\LibraryBundle\Monitoring\StatsStorageInterface;

class MonitoringWorkerConsumedMessageListenerTest extends TestCase
{
    /**
     * @var StatsStorageInterface $statsStorageInterface
     */
    private $statsStorageInterface;

    protected function getSubscriber(array $calls)
    {
        $this->statsStorageInterface = $this->getMockBuilder(StatsStorageInterface::class)->getMock();
        $this->statsStorageInterface->expects($calls['sendConsumerStat'])->method('sendConsumerStat');

        return new MonitoringWorkerConsumedMessageListener($this->statsStorageInterface, new NullLogger());
    }

    /**
     * @dataProvider dataProviderTestOnWorkerMessageReceived
     */
    public function testOnWorkerMessageReceived($methodCall, $envelopeValue)
    {
        $listener = $this->getSubscriber(['sendConsumerStat' => $methodCall]);

        $envelope = new Envelope($envelopeValue);
        $event = new WorkerMessageReceivedEvent($envelope, '');

        $listener->onWorkerMessageReceived($event);
    }

    public function dataProviderTestOnWorkerMessageReceived()
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
