<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle /blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Tests\Transport\Local;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\InvalidArgumentException;
use Symfony\Component\Messenger\Stamp\ErrorDetailsStamp;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;
use Vdm\Bundle\LibraryBundle\Stamp\StopAfterHandleStamp;
use Vdm\Bundle\LibraryBundle\Tests\Fixtures\AppBundle\Message\DefaultMessage;
use Vdm\Bundle\LibraryBundle\Transport\Local\VdmLocalTransport;

class VdmLocalTransportTest extends TestCase
{
    public function testGetFileIsNull()
    {
        $this->expectException(InvalidArgumentException::class);
        $serializer = $this->createMock(SerializerInterface::class);
        $transport = new VdmLocalTransport(new Filesystem(), null, $serializer);
        $transport->get();
    }

    public function testGet()
    {
        $file = __DIR__ . DIRECTORY_SEPARATOR . 'file.json';
        $message = new \stdClass();

        $serializer = $this->createMock(SerializerInterface::class);
        $serializer
            ->expects($this->once())
            ->method('decode')
            ->with(['key' => 'value'])
            ->willReturn(new Envelope($message));

        $transport = new VdmLocalTransport(new Filesystem(), $file, $serializer);
        $values = $transport->get();

        $this->assertCount(1, $values);
        $this->assertInstanceOf(StopAfterHandleStamp::class, $values[0]->last(StopAfterHandleStamp::class));
        $this->assertEquals($message, $values[0]->getMessage());
    }

    public function testSendNoError()
    {
        $data = ['key' => 'value'];
        $message = new DefaultMessage($data);
        $envelope = new Envelope($message);
        $output = json_encode($data, JSON_PRETTY_PRINT);

        $serializer = $this->createMock(SerializerInterface::class);
        $serializer
            ->expects($this->once())
            ->method('encode')
            ->with($envelope)
            ->willReturn($data);

        $filesystem = $this->createMock(Filesystem::class);
        $filesystem
            ->expects($this->once())
            ->method('dumpFile')
            ->with(__FILE__, $output);
        $filesystem
            ->expects($this->once())
            ->method('chmod')
            ->with(__FILE__, 0777);

        $transport = new VdmLocalTransport($filesystem, __FILE__, $serializer);
        $value = $transport->send($envelope);

        $this->assertEquals($envelope, $value);
    }

    public function testSendWithError()
    {
        $file = __DIR__ . DIRECTORY_SEPARATOR . 'file.json';
        $errorFile = __DIR__ . DIRECTORY_SEPARATOR . 'file-failed.json';

        $data = ['key' => 'value'];
        $message = new DefaultMessage($data);
        $envelope = new Envelope($message, [new ErrorDetailsStamp(\Exception::class, 1, 'myexceptionerror')]);
        $output = json_encode($data, JSON_PRETTY_PRINT);

        $serializer = $this->createMock(SerializerInterface::class);
        $serializer
            ->expects($this->once())
            ->method('encode')
            ->with($envelope)
            ->willReturn($data);

        $filesystem = $this->createMock(Filesystem::class);
        $filesystem
            ->expects($this->once())
            ->method('dumpFile')
            ->with($errorFile, $output);
        $filesystem
            ->expects($this->once())
            ->method('chmod')
            ->with($errorFile, 0777);

        $transport = new VdmLocalTransport($filesystem, $file, $serializer);
        $value = $transport->send($envelope);

        $this->assertEquals($envelope, $value);
    }
}
