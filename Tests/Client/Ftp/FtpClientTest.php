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

class FtpClientTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject $logger
     */
    private $logger;

    /**
     * @var FtpClient $ftpClient
     */
    private $ftpClient;

    protected function setUp(): void
    {
        $this->logger = $this->getMockBuilder(\Psr\Log\LoggerInterface::class)->getMock();
        $this->filesystem = $this->getMockBuilder(FilesystemInterface::class)->getMock();
        $this->ftpClient = new FtpClient('localhost', 22, '', '', true, [], $this->logger);
        $this->ftpClient->setFileSystem($this->filesystem);
    }

    public function testGetSftp()
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

        $responseGet = $this->ftpClient->get($file);

        $this->assertArrayHasKey("content", $responseGet);
    }

    public function testGetFtp()
    {
        $ftpClient = new FtpClient('localhost', 22, '', '', false, [], $this->logger);
        $ftpClient->setFileSystem($this->filesystem);
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
        $fileGet = $ftpClient->get($file);

        $this->assertArrayHasKey("content", $fileGet);
    }
        
    public function testListFailed()
    {
        $path = "PFE/SAS01/lol";
        $files = $this->ftpClient->list($path);

        $this->assertNull($files);
    }

    public function testList()
    {
        $this->filesystem->expects($this->once())->method('has')->willReturn(true);
        $this->filesystem->expects($this->once())->method('listContents');

        $path = "PFE/SAS01/";
        $this->ftpClient->list($path);
    }

    public function testGetFileSystem()
    {
        $filesystem = $this->ftpClient->getFileSystem();

        $this->assertInstanceOf(FilesystemInterface::class, $filesystem);
    }
}