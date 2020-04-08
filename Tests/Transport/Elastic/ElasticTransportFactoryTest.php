<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Transport\Elastic;

use PHPUnit\Framework\TestCase;
use Vdm\Bundle\LibraryBundle\Client\Elastic\ElasticClient;

class ElasticTransportFactoryTest extends TestCase
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
     * @var \PHPUnit_Framework_MockObject_MockObject $elasticClientFactory
     */
    private $elasticClientFactory;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject $elasticClientBehaviorFactoryRegistry
     */
    private $elasticClientBehaviorFactoryRegistry;

    /**
     * @var ElasticClient $elasticClient
     */
    private $elasticClient;

    /**
     * @var ElasticTransportFactory $elasticTransportFactory
     */
    private $elasticTransportFactory;

    protected function setUp(): void
    {
        $this->logger = $this->getMockBuilder(\Psr\Log\LoggerInterface::class)->getMock();
        $this->serializer = $this->getMockBuilder(\Symfony\Component\Messenger\Transport\Serialization\SerializerInterface::class)->getMock();
        $this->elasticClient = new ElasticClient('elasticsearch', 9200, '', '', 'http', $this->logger);
        $this->elasticClientFactory = $this
                ->getMockBuilder(\Vdm\Bundle\LibraryBundle\Client\Elastic\ElasticClientFactory::class)
                ->setConstructorArgs([$this->logger])
                ->getMock();
        $this->elasticClientBehaviorFactoryRegistry = $this
                        ->getMockBuilder(
                            \Vdm\Bundle\LibraryBundle\Client\Elastic\Behavior\ElasticClientBehaviorFactoryRegistry::class
                        )
                        ->setConstructorArgs([$this->logger])
                        ->setMethods(['create'])
                        ->getMock();
        
        $this->elasticClientBehaviorFactoryRegistry->method('create')->willReturn($this->elasticClient);
        $this->elasticTransportFactory = new ElasticTransportFactory($this->logger, $this->elasticClientFactory, $this->elasticClientBehaviorFactoryRegistry);
    }

    public function testCreateTransport()
    {
        $dsn = "elasticsearch://localhost:9200";
        $options = [
            'es_conf' => [],
        ];
        $transport = $this->elasticTransportFactory->createTransport($dsn, $options, $this->serializer);

        $this->assertInstanceOf(\Vdm\Bundle\LibraryBundle\Transport\Elastic\ElasticTransport::class, $transport);
    }

    /**
     * @dataProvider dataProviderTestSupport
     */
    public function testSupports($dsn, $value)
    {
        $bool = $this->elasticTransportFactory->supports($dsn, []);

        $this->assertEquals($bool, $value);
    }

    public function dataProviderTestSupport()
    {
        yield [
            "elasticsearch://localhost:9200",
            true
        ];
        yield [
            "https://ipconfig.io/json",
            false
        ];

    }
}
