<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Tests\EventListener;

use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Symfony\Component\Messenger\Event\WorkerRunningEvent;
use Vdm\Bundle\LibraryBundle\EventListener\StopWorkerRunningListener;
use Vdm\Bundle\LibraryBundle\Service\StopWorkerService;

class StopWorkerRunningListenerTest extends TestCase
{
    public function testOnWorkerRunning()
    {
        $worker = $this
                    ->getMockBuilder(\Symfony\Component\Messenger\Worker::class)
                    ->disableOriginalConstructor()
                    ->setMethods(['stop'])
                    ->getMock();
        $service = $this->getMockBuilder(StopWorkerService::class)->setMethods(['getFlag'])->getMock();
        $event = new WorkerRunningEvent($worker, true);
        $service->expects($this->once())->method('getFlag')->willReturn(true);
        $worker->expects($this->once())->method('stop');

        $listener = new StopWorkerRunningListener($service, new NullLogger());
        $listener->onWorkerRunning($event);
    }
}
