<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Tests\Client\Ftp;

use League\Flysystem\FilesystemInterface;
use PHPUnit\Framework\TestCase;
use Vdm\Bundle\LibraryBundle\Client\Ftp\FtpClient;
use Vdm\Bundle\LibraryBundle\Client\Ftp\MonitoringFtpClientBehavior;

class MonitoringFtpClientBehaviorTest extends TestCase
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
     * @var MonitoringFtpClientBehavior $monitoringFtpClient
     */
    private $monitoringFtpClient;

    protected function setUp(): void
    {
        $this->logger = $this->getMockBuilder(\Psr\Log\LoggerInterface::class)->getMock();
        $this->filesystem = $this->getMockBuilder(FilesystemInterface::class)->getMock();
        $this->ftpClient = new FtpClient('localhost', 22, '', '', true, [], $this->logger);
        $this->ftpClient->setFileSystem($this->filesystem);
        $this->eventDispatcher = $this->getMockBuilder(\Psr\EventDispatcher\EventDispatcherInterface::class)->getMock();

        $this->monitoringFtpClient = new MonitoringFtpClientBehavior($this->logger, $this->ftpClient, $this->eventDispatcher);
    }

    public function testGet()
    {
        $file = [
            "path" => "PFE/SAS01/[SpecifToulouse]_[V_Contrat].csv",
            "timestamp" => 1586341946,
            "type" => "file",
            "visibility" => "public",
            "size" => 1492,
            "dirname" => "PFE/SAS01",
            "basename" => "[SpecifToulouse]_[V_Contrat].csv",
            "extension" => "csv",
            "filename" => "[SpecifToulouse]_[V_Contrat]"
        ];        
        
        $fileGet = $this->monitoringFtpClient->get($file);

        $this->assertArrayHasKey("content", $fileGet);
    }

    public function testGetException()
    {
        $file = [
            "path" => "PFE/SAS01/[SpecifToulouse]_[V_Contrat].csv",
            "timestamp" => 1586341946,
            "type" => "file",
            "visibility" => "public",
            "size" => 1492,
            "dirname" => "PFE/SAS01",
            "basename" => "[SpecifToulouse]_[V_Contrat].csv",
            "extension" => "csv",
            "filename" => "[SpecifToulouse]_[V_Contrat]"
        ]; 
        $ftpClient = $this
                    ->getMockBuilder(FtpClient::class)
                    ->disableOriginalConstructor()
                    ->setMethods(['get'])
                    ->getMock();

        $ftpClient->method('get')->willThrowException(new \Exception());
        $this->expectException(\Exception::class);
        $this->eventDispatcher->expects($this->once())->method('dispatch');
        
        $this->monitoringFtpClientException = new MonitoringFtpClientBehavior($this->logger, $ftpClient, $this->eventDispatcher);
        $this->monitoringFtpClientException->get($file);
    }

    public function testListFailed()
    {
        $path = "PFE/SAS01/lol";
        $files = $this->monitoringFtpClient->list($path);

        $this->assertNull($files);
    }

    public function testList()
    {
        $this->filesystem->expects($this->once())->method('has')->willReturn(true);
        $this->filesystem->expects($this->once())->method('listContents');

        $path = "PFE/SAS01/";
        $this->monitoringFtpClient->list($path);
    }
}
