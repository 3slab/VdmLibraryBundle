<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle /blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Tests\Transport\Local;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Exception\InvalidArgumentException;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;
use Vdm\Bundle\LibraryBundle\Transport\Local\VdmLocalTransport;
use Vdm\Bundle\LibraryBundle\Transport\Local\VdmLocalTransportFactory;

/**
 * Class VdmLocalTransportFactoryTest
 *
 * @package Vdm\Bundle\LibraryBundle\Tests\Transport\Local
 */
class VdmLocalTransportFactoryTest extends TestCase
{
    public function testSupports()
    {
        $factory = new VdmLocalTransportFactory();

        $this->assertFalse($factory->supports('http://url.com', []));
        $this->assertFalse($factory->supports('   vdm+local:///my/path/to/my/file', []));
        $this->assertTrue($factory->supports('vdm+local:///my/path/to/my/file', []));
    }

    public function testCreateTransportFileDoesNotExist()
    {
        $this->expectException(InvalidArgumentException::class);

        $serializer = $this->createMock(SerializerInterface::class);

        $factory = new VdmLocalTransportFactory();
        $factory->createTransport('vdm+local:///my/path/to/my/file', [], $serializer);
    }

    public function testCreateTransport()
    {
        $serializer = $this->createMock(SerializerInterface::class);
        $file = __FILE__;

        $factory = new VdmLocalTransportFactory();
        $transport = $factory->createTransport("vdm+local://$file", [], $serializer);

        $this->assertInstanceOf(VdmLocalTransport::class, $transport);
    }
}
