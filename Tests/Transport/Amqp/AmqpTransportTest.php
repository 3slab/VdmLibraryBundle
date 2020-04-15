<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Transport\Amqp;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Transport\AmqpExt\AmqpSender;
use Symfony\Component\Messenger\Transport\AmqpExt\Connection;
use Symfony\Component\Messenger\Transport\Sender\SenderInterface;
use Vdm\Bundle\LibraryBundle\Model\Message;

class AmqpTransportTest extends TestCase
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
     * @var AmqpTransport $amqpTransport
     */
    private $amqpTransport;

    protected function setUp(): void
    {
        $this->logger = $this->getMockBuilder(\Psr\Log\LoggerInterface::class)->getMock();
        $this->serializer = $this->getMockBuilder(\Symfony\Component\Messenger\Transport\Serialization\SerializerInterface::class)->getMock();
        $this->connection = $this->getMockBuilder(Connection::class)->disableOriginalConstructor()->getMock();
        $this->amqpTransport = $this
                            ->getMockBuilder(AmqpTransport::class)
                            ->setConstructorArgs([$this->logger, $this->connection, $this->serializer])
                            ->setMethods(null)
                            ->getMock();
    }

    public function testGet()
    {
        $iterable = $this->amqpTransport->get();

        $this->assertNull($iterable->current());
    }

    public function testGetMessageCount()
    {
        $int = $this->amqpTransport->getMessageCount();

        $this->assertEquals(0, $int);
    }
}
