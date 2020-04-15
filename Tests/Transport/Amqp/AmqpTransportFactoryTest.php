<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Transport\Amqp;

use PHPUnit\Framework\TestCase;

class AmqpTransportFactoryTest extends TestCase
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
     * @var AmqpTransport $amqpTransportFactory
     */
    private $amqpTransportFactory;

    protected function setUp(): void
    {
        $this->logger = $this->getMockBuilder(\Psr\Log\LoggerInterface::class)->getMock();
        $this->serializer = $this->getMockBuilder(\Symfony\Component\Messenger\Transport\Serialization\SerializerInterface::class)->getMock();
        $this->amqpTransportFactory = new AmqpTransportFactory($this->logger);
    }

    public function testCreateTransport()
    {
        $dsn = "vdm+amqp://localhost:9200";
        $options = [];

        $transport = $this->amqpTransportFactory->createTransport($dsn, $options, $this->serializer);

        $this->assertInstanceOf(\Vdm\Bundle\LibraryBundle\Transport\Amqp\AmqpTransport::class, $transport);
    }    

    /**
     * @dataProvider dataProviderTestSupport
     */
    public function testSupports($dsn, $value)
    {
        $bool = $this->amqpTransportFactory->supports($dsn, []);

        $this->assertEquals($bool, $value);
    }

    public function dataProviderTestSupport()
    {
        yield [
            "vdm+amqp://localhost:9200",
            true
        ];
        yield [
            "https://ipconfig.io/json",
            false
        ];

    }
}
