<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Tests\EventListener;

use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Symfony\Component\Messenger\Event\WorkerStartedEvent;
use Vdm\Bundle\LibraryBundle\EventListener\MonitoringWorkerStartedListener;
use Vdm\Bundle\LibraryBundle\Monitoring\StatsStorageInterface;

class MonitoringWorkerStartedListenerTest extends TestCase
{
    /**
     * @var StatsStorageInterface $statsStorageInterface
     */
    private $statsStorageInterface;

    protected function getSubscriber(array $calls)
    {
        $this->statsStorageInterface = $this->getMockBuilder(StatsStorageInterface::class)->getMock();
        $this->worker = $this->getMockBuilder(\Symfony\Component\Messenger\Worker::class)->disableOriginalConstructor()->getMock();
        $this->worker = $this->getMockBuilder(\Symfony\Component\Messenger\Worker::class)->disableOriginalConstructor()->getMock();
        $this->statsStorageInterface->expects($calls['sendRunningStat'])->method('sendRunningStat');

        return new MonitoringWorkerStartedListener($this->statsStorageInterface, new NullLogger());
    }

    /**
     * @dataProvider dataProviderTestOnWorkerStarted
     */
    public function testOnWorkerStarted($methodCall)
    {
        $listener = $this->getSubscriber(['sendRunningStat' => $methodCall]);

        $event = new WorkerStartedEvent($this->worker);

        $listener->onWorkerStarted($event);
    }

    public function dataProviderTestOnWorkerStarted()
    {
        yield [
            $this->once()
        ];
    }
}
