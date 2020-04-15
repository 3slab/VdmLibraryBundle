<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Transport\Ftp;

use PHPUnit\Framework\TestCase;
use Vdm\Bundle\LibraryBundle\Client\Ftp\FtpClient;
use Vdm\Bundle\LibraryBundle\Executor\Ftp\DefaultFtpExecutor;
use Vdm\Bundle\LibraryBundle\Transport\Ftp\FtpTransportFactory;

class FtpTransportFactoryTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject $logger
     */
    private $logger;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject $serializer
     */
    private $serializer;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject $ftpClientFactory
     */
    private $ftpClientFactory;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject $ftpClientBehaviorFactoryRegistry
     */
    private $ftpClientBehaviorFactoryRegistry;

    /**
     * @var FtpClient $ftpClient
     */
    private $ftpClient;

    /**
     * @var DefaultHttpExecutor $ftpExecutor
     */
    private $ftpExecutor;

    /**
     * @var FtpTransportFactory $ftpTransportFactory
     */
    private $ftpTransportFactory;


    protected function setUp(): void
    {
        $this->logger = $this->getMockBuilder(\Psr\Log\LoggerInterface::class)->getMock();
        $this->serializer = $this->getMockBuilder(\Symfony\Component\Messenger\Transport\Serialization\SerializerInterface::class)->getMock();
        $this->ftpClientFactory = $this->getMockBuilder(\Vdm\Bundle\LibraryBundle\Client\Ftp\FtpClientFactoryInterface::class)->getMock();
        $this->ftpClient = new FtpClient('localhost', 22, '', '', true, [], $this->logger);
        $this->ftpExecutor = new DefaultFtpExecutor();
        $this->ftpClientBehaviorFactoryRegistry = $this
                        ->getMockBuilder(
                            \Vdm\Bundle\LibraryBundle\Client\Ftp\Behavior\FtpClientBehaviorFactoryRegistry::class
                        )
                        ->setConstructorArgs([$this->logger])
                        ->setMethods(['create'])
                        ->getMock();
        
        $this->ftpClientBehaviorFactoryRegistry->method('create')->willReturn($this->ftpClient);
        $this->ftpTransportFactory = new FtpTransportFactory($this->logger, $this->ftpClientFactory, $this->ftpExecutor, $this->ftpClientBehaviorFactoryRegistry);
    }

    public function testCreateTransport()
    {
        $dsn = "sftp://localhost:22";
        $options = [
            'mode' => "move",
            'ftp_options' => [
                'dirpath' => "PFE/SAS01/",
                'storage' => "PFE/SAS01/Storage",
            ],
        ];
        $transport = $this->ftpTransportFactory->createTransport($dsn, $options, $this->serializer);

        $this->assertInstanceOf(\Vdm\Bundle\LibraryBundle\Transport\Ftp\FtpTransport::class, $transport);
    }

    public function testCreateTransportInvalidArgumentDirpathException()
    {
        $dsn = "sftp://localhost:22";
        $options = [
            'mode' => "move",
            'ftp_options' => [
                'storage' => "PFE/SAS01/Storage",
            ],
        ];

        $ftpTransportFactory = $this
                        ->getMockBuilder(FtpTransportFactory::class)
                        ->disableOriginalConstructor()
                        ->setMethods(null)
                        ->getMock();

        $this->expectException(\InvalidArgumentException::class);

        $ftpTransportFactory->createTransport($dsn, $options, $this->serializer);
    }

    public function testCreateTransportInvalidArgumentStorageException()
    {
        $dsn = "sftp://localhost:22";
        $options = [
            'mode' => "move",
            'ftp_options' => [
                'dirpath' => "PFE/SAS01/",
            ],
        ];

        $ftpTransportFactory = $this
                        ->getMockBuilder(FtpTransportFactory::class)
                        ->disableOriginalConstructor()
                        ->setMethods(null)
                        ->getMock();

        $this->expectException(\InvalidArgumentException::class);

        $ftpTransportFactory->createTransport($dsn, $options, $this->serializer);
    }

    /**
     * @dataProvider dataProviderTestSupport
     */
    public function testSupports($dsn, $value)
    {
        $bool = $this->ftpTransportFactory->supports($dsn, []);

        $this->assertEquals($bool, $value);
    }

    public function dataProviderTestSupport()
    {
        yield [
            "ftp://ftp.net:2222",
            true
        ];
        yield [
            "sftp://test:test@sftp.net:2222",
            true
        ];
        yield [
            "http://ipconfig.io/json",
            false
        ];

    }
}
