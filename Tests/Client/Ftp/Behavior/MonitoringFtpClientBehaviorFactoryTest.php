<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Tests\Client\Ftp\Behavior;

use PHPUnit\Framework\TestCase;
use Vdm\Bundle\LibraryBundle\Client\Ftp\Behavior\MonitoringFtpClientBehaviorFactory;

class MonitoringFtpClientBehaviorFactoryTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject $logger
     */
    private $logger;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject $ftpClient
     */
    private $ftpClient;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject $eventDispatcher
     */
    private $eventDispatcher;

    /**
     * @var MonitoringFtpClientBehavior $monitoringFtpClient
     */
    private $monitoringFtpClient;

    protected function setUp(): void
    {
        $this->logger = $this->getMockBuilder(\Psr\Log\LoggerInterface::class)->getMock();
        $this->ftpClient = $this->getMockBuilder(\Vdm\Bundle\LibraryBundle\Client\Ftp\FtpClientInterface::class)->getMock();
        $this->eventDispatcher = $this->getMockBuilder(\Psr\EventDispatcher\EventDispatcherInterface::class)->getMock();

        $this->monitoringFtpClient = new MonitoringFtpClientBehaviorFactory($this->eventDispatcher);
    }

    public function testPriority()
    {
        $monitoring = MonitoringFtpClientBehaviorFactory::priority(5);

        $this->assertEquals(5, $monitoring);
    }
    
    public function testCreateDecoratedFtpClient()
    {
        $monitoringFtpClient = $this->monitoringFtpClient->createDecoratedFtpClient($this->logger, $this->ftpClient, []);
        
        $this->assertInstanceOf(\Vdm\Bundle\LibraryBundle\Client\Ftp\MonitoringFtpClientBehavior::class, $monitoringFtpClient);
    }

    public function testSupport()
    {
        $options["monitoring"] = [
            "enabled" => true
        ];
        $result = $this->monitoringFtpClient->support($options);

        $this->assertTrue($result);
    }

    public function testNotSupport()
    {
        $options["monitoring"] = [
            "enabled" => false
        ];
        $result = $this->monitoringFtpClient->support($options);

        $this->assertFalse($result);
    }
}
