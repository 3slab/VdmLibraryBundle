<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Tests\Client\Ftp\Behavior;

use PHPUnit\Framework\TestCase;
use Vdm\Bundle\LibraryBundle\Client\Ftp\Behavior\FtpClientBehaviorFactoryRegistry;
use Vdm\Bundle\LibraryBundle\Client\Ftp\Behavior\MonitoringFtpClientBehaviorFactory;
use Vdm\Bundle\LibraryBundle\Client\Ftp\FtpClient;

class FtpClientBehaviorFactoryRegistryTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject $logger
     */
    private $logger;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject $eventDispatcher
     */
    private $eventDispatcher;

    /**
     * @var FtpClient $ftpClient
     */
    private $ftpClient;

    /**
     * @var FtpClientBehaviorFactoryRegistry $ftpClientBehavior
     */
    private $ftpClientBehavior;

    protected function setUp(): void
    {
        $this->logger = $this->getMockBuilder(\Psr\Log\LoggerInterface::class)->getMock();
        $this->eventDispatcher = $this->getMockBuilder(\Psr\EventDispatcher\EventDispatcherInterface::class)->getMock();
        $this->ftpClient = new FtpClient('localhost', 22, '', '', true, [], $this->logger);

        $this->ftpClientBehavior = new FtpClientBehaviorFactoryRegistry($this->logger);
    }

    public function testAddFactory()
    {
        $monitoringrFtpClientBehaviorFactory = new MonitoringFtpClientBehaviorFactory($this->eventDispatcher);
        $priorityMonitoring = 0;

        $property = new \ReflectionProperty(\Vdm\Bundle\LibraryBundle\Client\Ftp\Behavior\FtpClientBehaviorFactoryRegistry::class, 'ftpClientBehavior');
        $property->setAccessible(true);
        $value = $property->getValue($this->ftpClientBehavior);
        $this->assertEmpty($value);
        try {
            $this->ftpClientBehavior->addFactory($monitoringrFtpClientBehaviorFactory, $priorityMonitoring);
        } catch (\Exception $exception) {

        }

        $value = $property->getValue($this->ftpClientBehavior);
        $this->assertNotEmpty($value);
        $this->assertCount(1, $value);
    }

    public function testCreate()
    {
        $ftpClient = $this->ftpClientBehavior->create($this->ftpClient, []);

        $this->assertInstanceOf(FtpClient::class, $ftpClient);
    }

    public function testCreateNotSupport()
    {
        $ftpClient = $this->ftpClientBehavior->create($this->ftpClient, []);

        $this->assertInstanceOf(FtpClient::class, $ftpClient);
    }

    public function testCreateSupport()
    {
        $monitoringrFtpClientBehaviorFactory = new MonitoringFtpClientBehaviorFactory($this->eventDispatcher);
        $priorityMonitoring = 0;
        $this->ftpClientBehavior->addFactory($monitoringrFtpClientBehaviorFactory, $priorityMonitoring);
        $ftpClient = $this->ftpClientBehavior->create($this->ftpClient, ['monitoring' => ['enabled' => true]]);

        $this->assertInstanceOf(\Vdm\Bundle\LibraryBundle\Client\Ftp\MonitoringFtpClientBehavior::class, $ftpClient);
    }
}
