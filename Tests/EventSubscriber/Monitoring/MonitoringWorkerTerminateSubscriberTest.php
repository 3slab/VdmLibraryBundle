<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Tests\EventSubscriber\Monitoring;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Vdm\Bundle\LibraryBundle\EventSubscriber\Monitoring\MonitoringWorkerTerminateSubscriber;
use Vdm\Bundle\LibraryBundle\Service\Monitoring\Monitoring;
use Vdm\Bundle\LibraryBundle\Service\Monitoring\MonitoringService;

class MonitoringWorkerTerminateSubscriberTest extends TestCase
{
    public function testConsoleTerminateEventSentMetricAndFlush()
    {
        $storage = $this->createMock(MonitoringService::class);

        $storage->expects($this->once())
            ->method('update')
            ->with(Monitoring::RUNNING_STAT, 0);

        $storage->expects($this->once())
            ->method('flush')
            ->with();

        $command = $this->createMock(Command::class);
        $input = $this->createMock(InputInterface::class);
        $output = $this->createMock(OutputInterface::class);

        $event = new ConsoleTerminateEvent($command, $input, $output, 1);

        $subscriber = new MonitoringWorkerTerminateSubscriber($storage);
        $subscriber->onConsoleTerminateEvent($event);
    }
}
