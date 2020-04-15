<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Tests\Client\Ftp;

use PHPUnit\Framework\TestCase;
use Vdm\Bundle\LibraryBundle\Client\Ftp\FtpClientFactory;

class FtpClientFactoryTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject $logger
     */
    private $logger;

    /**
     * @var FtpClientFactory $ftpClientFactory
     */
    private $ftpClientFactory;

    protected function setUp(): void
    {
        $this->logger = $this->getMockBuilder(\Psr\Log\LoggerInterface::class)->getMock();
        $this->ftpClientFactory = new FtpClientFactory($this->logger);
    }

    public function testCreate()
    {    
        $ftpClient = $this->ftpClientFactory->create("sftp://localhost:22", []);

        $this->assertInstanceOf(\Vdm\Bundle\LibraryBundle\Client\Ftp\FtpClient::class, $ftpClient);
    }
}
