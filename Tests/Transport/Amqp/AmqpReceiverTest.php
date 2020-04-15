<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Test\Transport\Amqp;

use Symfony\Component\Messenger\Transport\AmqpExt\Connection;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\TransportException;
use Vdm\Bundle\LibraryBundle\Model\Message;
use Vdm\Bundle\LibraryBundle\Transport\Amqp\AmqpReceiver;
use Symfony\Component\Messenger\Exception\MessageDecodingFailedException;

class AmqpReceiverTest extends TestCase
{
    /**
     * @dataProvider dataProviderTestGet
     */
    public function testGet($ExceptionGet, $getReturn, $jsonValide)
    {
        $logger = $this->getMockBuilder(\Psr\Log\LoggerInterface::class)->getMock();
        $serializer = $this->getMockBuilder(\Symfony\Component\Messenger\Transport\Serialization\SerializerInterface::class)->getMock();
        $amqpException = new \AMQPException('');
        
        $connection = $this
                            ->getMockBuilder(Connection::class)
                            ->disableOriginalConstructor()
                            ->setMethods(['getQueueNames', 'get'])
                            ->getMock();
        $connection->method('getQueueNames')->willReturn(['test']);
        if ($ExceptionGet) {
            $connection->method('get')->willThrowException($amqpException);
            $this->expectException(TransportException::class);
        } else {
            if ($getReturn === null) {
                $connection->method('get')->willReturn($getReturn);
            } else {
                $amqpEnvelope = $this->getMockBuilder(\AMQPEnvelope::class)->disableOriginalConstructor()->setMethods(['getBody'])->getMock();
                $amqpEnvelope->method('getBody')->willReturn($getReturn);
                $connection->method('get')->willReturn($amqpEnvelope);
                if (!$jsonValide) {
                    $this->expectException(MessageDecodingFailedException::class);
                }
            }
        }

        $amqpReceiver = $this
                            ->getMockBuilder(AmqpReceiver::class)
                            ->setConstructorArgs([$connection, $serializer])
                            ->setMethods(['rejectAmqpEnvelope'])
                            ->getMock();
        $generator = $amqpReceiver->get();
        $result = $generator->current();

        if ($jsonValide) {
            $this->assertInstanceOf(Envelope::class, $result);
        } else {
            $this->assertNull($result);
        }
    }

    public function dataProviderTestGet()
    {
        yield [
            true,
            null,
            false
        ];
        yield [
            false,
            null,
            false
        ];
        yield [
            false,
            "{message:\"test\"}",
            false
        ];
        yield [
            false,
            "{\"message\":\"test\"}",
            true
        ];
    }
}
