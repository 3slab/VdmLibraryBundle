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
use Vdm\Bundle\LibraryBundle\Transport\Serialization\VdmSymfonySerializer;

/**
 * Class VdmSymfonySerializerTest
 * @package Vdm\Bundle\LibraryBundle\Tests\Transport\Serialization
 */
class VdmSymfonySerializerTest extends TestCase
{
    public function testEncode()
    {
        $envelope = new Envelope(new \stdClass());
        $encodeReturn = ['encoded' => 'result'];

        /** @var SerializerInterface $serializer */
        $serializer = $this->createMock(SerializerInterface::class);
        $serializer
            ->expects($this->once())
            ->method('encode')
            ->with($envelope)
            ->willReturn($encodeReturn);

        $vdmSerializer = new VdmSymfonySerializer($serializer);
        $result = $vdmSerializer->encode($envelope);

        $this->assertEquals($encodeReturn, $result);
    }

    public function testDecodeNoBody()
    {
        $this->expectException(MessageDecodingFailedException::class);

        /** @var SerializerInterface $serializer */
        $serializer = $this->createMock(SerializerInterface::class);

        $vdmSerializer = new VdmSymfonySerializer($serializer);
        $vdmSerializer->decode(['key' => 'value']);
    }

    public function testDecodeNoHeader()
    {
        $this->expectException(MessageDecodingFailedException::class);

        /** @var SerializerInterface $serializer */
        $serializer = $this->createMock(SerializerInterface::class);

        $vdmSerializer = new VdmSymfonySerializer($serializer);
        $vdmSerializer->decode(['body' => 'value']);
    }

    public function testDecodeNoHeaderType()
    {
        $this->expectException(MessageDecodingFailedException::class);

        /** @var SerializerInterface $serializer */
        $serializer = $this->createMock(SerializerInterface::class);

        $vdmSerializer = new VdmSymfonySerializer($serializer);
        $vdmSerializer->decode(['body' => 'value', 'headers' => ['key' => 'head1']]);
    }

    public function testDecodeOverrideDecodeClassDoesNotExist()
    {
        $this->expectException(MessageDecodingFailedException::class);

        /** @var SerializerInterface $serializer */
        $serializer = $this->createMock(SerializerInterface::class);

        $vdmSerializer = new VdmSymfonySerializer($serializer, 'Class\\That\\Does\\Not\\Exists');
        $vdmSerializer->decode(['body' => 'value', 'headers' => ['type' => 'head1']]);
    }

    public function testDecodeOverrideDecodeClass()
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

        $vdmSerializer = new VdmSymfonySerializer($serializer, AnotherMessage::class);
        $result = $vdmSerializer->decode([
            'body' => $body,
            'headers' => ['type' => DefaultMessage::class]
        ]);

        $this->assertEquals($decodedReturn, $result->getMessage());
    }
}