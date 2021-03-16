<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Tests\EventSubscriber;

use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\OutputInterface;
use Symfony\Component\Console\Output\OutputInterface as OutputOutputInterface;
use Symfony\Component\Console\Tester\CommandTester;
use Vdm\Bundle\LibraryBundle\EventSubscriber\MonitoringConsoleTerminateListener;
use Vdm\Bundle\LibraryBundle\Monitoring\StatsStorageInterface;

class MonitoringConsoleTerminateListenerTest extends TestCase
{
    public function testOnConsoleTerminate()
    {
        $statsStorageInterface = $this->getMockBuilder(StatsStorageInterface::class)->getMock();
        // $event = $this->getMockBuilder(ConsoleTerminateEvent::class)->disableOriginalConstructor()->getMock();
        $command = $this->getMockBuilder(Command::class)->disableOriginalConstructor()->getMock();
        $input = $this->getMockBuilder(InputInterface::class)->getMock();
        $output = $this->getMockBuilder(OutputOutputInterface::class)->getMock();
        $event = new ConsoleTerminateEvent($command, $input, $output, 1);

        $statsStorageInterface->expects($this->once())->method('sendRunningStat');
        $statsStorageInterface->expects($this->once())->method('sendErrorStateStat');
        $statsStorageInterface->expects($this->once())->method('flush')->with(true);

        $listener = new MonitoringConsoleTerminateListener($statsStorageInterface, new NullLogger());
        $listener->onConsoleTerminate($event);
    }
}
