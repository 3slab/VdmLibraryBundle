<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle /blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Tests\Transport\Serialization;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\MessageDecodingFailedException;
use Symfony\Component\Messenger\Transport\Serialization\Serializer;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;
use Symfony\Component\Serializer\SerializerInterface as SymfonySerializerInterface;
use Vdm\Bundle\LibraryBundle\Tests\Fixtures\AppBundle\Message\AnotherMessage;
use Vdm\Bundle\LibraryBundle\Tests\Fixtures\AppBundle\Message\DefaultMessage;
use Vdm\Bundle\LibraryBundle\Transport\Serialization\RawMessageSerializer;
use Vdm\Bundle\LibraryBundle\Transport\Serialization\VdmSymfonySerializer;

/**
 * Class RawMessageSerializerTest
 * @package Vdm\Bundle\LibraryBundle\Tests\Transport\Serialization
 */
class RawMessageSerializerTest extends TestCase
{
    public function testEncode()
    {
        $payload = ['key1' => 'value1'];
        $message = new AnotherMessage($payload);
        $envelope = new Envelope($message);
        $encodedReturn = ['payload' => $payload];

        /** @var SymfonySerializerInterface $serializer */
        $sfSerializer = $this->createMock(SymfonySerializerInterface::class);
        $sfSerializer
            ->expects($this->once())
            ->method('serialize')
            ->with($message)
            ->willReturn($encodedReturn);
        $serializer = new Serializer($sfSerializer);

        $vdmSerializer = new RawMessageSerializer($serializer);
        $result = $vdmSerializer->encode($envelope);

        $this->assertEquals($encodedReturn, $result);
    }

    public function testDecodeClassDoesNotExist()
    {
        $this->expectException(MessageDecodingFailedException::class);

        /** @var SerializerInterface $serializer */
        $serializer = $this->createMock(SerializerInterface::class);

        $vdmSerializer = new RawMessageSerializer($serializer, 'Class\\That\\Does\\Not\\Exists');
        $vdmSerializer->decode(['body' => 'value', 'headers' => ['type' => 'head1']]);
    }

    public function testDecode()
    {
        $payload = ['key1' => 'value1'];
        $body = ['payload' => $payload];
        $decodedReturn = new AnotherMessage($payload);

        /** @var SymfonySerializerInterface $serializer */
        $sfSerializer = $this->createMock(SymfonySerializerInterface::class);
        $sfSerializer
            ->expects($this->once())
            ->method('deserialize')
            ->with($body, AnotherMessage::class, 'json', ['messenger_serialization' => true])
            ->willReturn($decodedReturn);
        $serializer = new Serializer($sfSerializer);

        $vdmSerializer = new RawMessageSerializer($serializer, AnotherMessage::class);
        $result = $vdmSerializer->decode($body);

        $this->assertEquals($decodedReturn, $result->getMessage());
    }
}
