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
use Symfony\Component\Messenger\Event\WorkerMessageFailedEvent;
use Vdm\Bundle\LibraryBundle\EventListener\MonitoringWorkerMessageFailedListener;
use Vdm\Bundle\LibraryBundle\Model\Message;
use Vdm\Bundle\LibraryBundle\Monitoring\StatsStorageInterface;

class MonitoringWorkerMessageFailedListenerTest extends TestCase
{
    /**
     * @var StatsStorageInterface $statsStorageInterface
     */
    private $statsStorageInterface;

    protected function getSubscriber(array $calls)
    {
        $this->statsStorageInterface = $this->getMockBuilder(StatsStorageInterface::class)->getMock();
        $this->exception = $this->getMockBuilder(\Throwable::class)->getMock();
        $this->statsStorageInterface->expects($calls['sendErrorStat'])->method('sendErrorStat');

        return new MonitoringWorkerMessageFailedListener($this->statsStorageInterface, new NullLogger());
    }

    /**
     * @dataProvider dataProviderTestOnWorkerMessageFailed
     */
    public function testOnWorkerMessageFailed($methodCall, $envelopeValue)
    {
        $listener = $this->getSubscriber(['sendErrorStat' => $methodCall]);

        $envelope = new Envelope($envelopeValue);
        $event = new WorkerMessageFailedEvent($envelope, '', $this->exception);

        $listener->onWorkerMessageFailed($event);
    }

    public function dataProviderTestOnWorkerMessageFailed()
    {
        yield [
            $this->never(),
            new \stdClass(),
        ];
        yield [
            $this->once(),
            new Message(''),
        ];
    }
}
