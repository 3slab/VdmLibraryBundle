<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Tests\EventSubscriber;

use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Symfony\Component\Messenger\Event\WorkerRunningEvent;
use Vdm\Bundle\LibraryBundle\EventSubscriber\ErrorDuringMessageHandlerListener;
use Vdm\Bundle\LibraryBundle\EventSubscriber\MonitoringWorkerRunningListener;
use Vdm\Bundle\LibraryBundle\Monitoring\StatsStorageInterface;

class MonitoringWorkerRunningListenerTest extends TestCase
{
    /**
     * @var StatsStorageInterface $statsStorageInterface
     */
    private $statsStorageInterface;

    protected function getSubscriber(array $calls)
    {
        $this->statsStorageInterface = $this->getMockBuilder(StatsStorageInterface::class)->getMock();
        $this->errodDuringMessageHandler = $this->getMockBuilder(ErrorDuringMessageHandlerListener::class)->getMock();
        $this->worker = $this->getMockBuilder(\Symfony\Component\Messenger\Worker::class)->disableOriginalConstructor()->getMock();
        $this->statsStorageInterface->expects($calls['sendErrorStateStat'])->method('sendErrorStateStat');

        return new MonitoringWorkerRunningListener($this->errodDuringMessageHandler, $this->statsStorageInterface, new NullLogger());
    }

    public function testOnWorkerRunningThrowable()
    {
        $worker = $this->getMockBuilder(\Symfony\Component\Messenger\Worker::class)->disableOriginalConstructor()->getMock();
        $statsStorageInterface = $this->getMockBuilder(StatsStorageInterface::class)->getMock();
        $errorListener = $this->getMockBuilder(ErrorDuringMessageHandlerListener::class)->getMock();
        $errorListener->method('getThrownException')->willReturn(new \Exception(''));
        $event = new WorkerRunningEvent($worker, false);

        $listener = new MonitoringWorkerRunningListener($errorListener, $statsStorageInterface, new NullLogger());
        $result = $listener->onWorkerRunning($event);

        $this->assertNull($result);
    }

    /**
     * @dataProvider dataProviderTestOnWorkerRunning
     */
    public function testOnWorkerRunning($methodCall)
    {
        $listener = $this->getSubscriber(['sendErrorStateStat' => $methodCall]);

        $event = new WorkerRunningEvent($this->worker, false);

        $listener->onWorkerRunning($event);
    }

    public function dataProviderTestOnWorkerRunning()
    {
        yield [
            $this->once()
        ];
    }
}
