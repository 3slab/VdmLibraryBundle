<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Tests\Transport\Http;

use PHPUnit\Framework\TestCase;
use Vdm\Bundle\LibraryBundle\Executor\Http\DefaultHttpExecutor;
use Vdm\Bundle\LibraryBundle\Transport\Http\HttpTransportFactory;

class HttpTransportFactoryTest extends TestCase
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
     * @var \PHPUnit_Framework_MockObject_MockObject $httpClient
     */
    private $httpClient;

    /**
     * @var DefaultHttpExecutor $httpExecutor
     */
    private $httpExecutor;

    protected function setUp(): void
    {
        $this->logger = $this->getMockBuilder(\Psr\Log\LoggerInterface::class)->getMock();
        $this->statsStorageInterface = $this->getMockBuilder(\Vdm\Bundle\LibraryBundle\Monitoring\StatsStorageInterface::class)->getMock();
        $this->httpClient = $this->getMockBuilder(\Symfony\Contracts\HttpClient\HttpClientInterface::class)->getMock();
        $this->serializer = $this->getMockBuilder(\Symfony\Component\Messenger\Transport\Serialization\SerializerInterface::class)->getMock();
        $this->httpExecutor = new DefaultHttpExecutor($this->logger, $this->serializer, $this->httpClient);
        $this->httpClientBehaviorFactoryRegistry = $this
                        ->getMockBuilder(
                            \Vdm\Bundle\LibraryBundle\Client\Http\Behavior\HttpClientBehaviorFactoryRegistry::class
                        )
                        ->setConstructorArgs([$this->logger])
                        ->setMethods(['create'])
                        ->getMock();
        
        $this->httpClientBehaviorFactoryRegistry->method('create')->willReturn($this->httpClient);
        $this->httpTransportFactory = new HttpTransportFactory($this->logger, $this->statsStorageInterface, $this->httpExecutor, $this->httpClientBehaviorFactoryRegistry);
    }

    public function testCreateTransport()
    {
        $dsn = "https://ipconfig.io/json";
        $options = [
            'method' => "GET",
            'http_options' => [],
        ];
        $transport = $this->httpTransportFactory->createTransport($dsn, $options, $this->serializer);

        $this->assertInstanceOf(\Vdm\Bundle\LibraryBundle\Transport\Http\HttpTransport::class, $transport);
    }

    /**
     * @dataProvider dataProviderTestSupport
     */
    public function testSupports($dsn, $value)
    {
        $bool = $this->httpTransportFactory->supports($dsn, []);

        $this->assertEquals($bool, $value);
    }

    public function dataProviderTestSupport()
    {
        yield [
            "http://ipconfig.io/json",
            true
        ];
        yield [
            "https://ipconfig.io/json",
            true
        ];
        yield [
            "sftp://ipconfig.io/json",
            false
        ];

    }
}
