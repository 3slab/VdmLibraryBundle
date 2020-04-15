<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Tests\Client\Ftp;

use League\Flysystem\FilesystemInterface;
use PHPUnit\Framework\TestCase;
use Vdm\Bundle\LibraryBundle\Client\Ftp\DecoratorFtpClient;
use Vdm\Bundle\LibraryBundle\Client\Ftp\FtpClient;

class DecoratorFtpClientTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject $logger
     */
    private $logger;

    /**
     * @var FtpClient $ftpClient
     */
    private $ftpClient;

    /**
     * @var DecoratorFtpClient $decoratorFtpClient
     */
    private $decoratorFtpClient;

    protected function setUp(): void
    {
        $this->logger = $this->getMockBuilder(\Psr\Log\LoggerInterface::class)->getMock();
        $this->filesystem = $this->getMockBuilder(FilesystemInterface::class)->getMock();
        $this->ftpClient = new FtpClient('localhost', 22, '', '', true, [], $this->logger);
        $this->ftpClient->setFileSystem($this->filesystem);
        $this->decoratorFtpClient = $this->getMockForAbstractClass(DecoratorFtpClient::class, [$this->logger, $this->ftpClient]);
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

        $fileGet = $this->decoratorFtpClient->get($file);

        $this->assertArrayHasKey("content", $fileGet);
    }

    public function testListFailed()
    {
        $path = "PFE/SAS01/lol";
        $return = $this->decoratorFtpClient->list($path);

        $this->assertNull($return);
    }

    public function testList()
    {
        $path = "PFE/SAS01/";
        $this->filesystem->expects($this->once())->method('has')->with($path)->willReturn(true);
        $this->filesystem->expects($this->once())->method('listContents')->with($path);
        
        $this->decoratorFtpClient->list($path);
    }

    public function testGetFileSystem()
    {
        $filesystem = $this->decoratorFtpClient->getFileSystem();

        $this->assertInstanceOf(FilesystemInterface::class, $filesystem);
    }
}
