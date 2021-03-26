<?php

/**
 * @package    3slab/VdmLibraryDoctrineOrmTransportBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryDoctrineOrmTransportBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Tests\Transport\Manual;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;
use TypeError;
use Vdm\Bundle\LibraryBundle\Tests\Message\DefaultMessage;
use Vdm\Bundle\LibraryBundle\Transport\Manual\VdmManualExecutorCollection;
use Vdm\Bundle\LibraryBundle\Transport\Manual\VdmManualTransport;
use Vdm\Bundle\LibraryBundle\Transport\Manual\VdmManualTransportFactory;

/**
 * Class VdmManualTransportFactoryTest
 *
 * @package Vdm\Bundle\LibraryBundle\Tests\Transport\Manual
 */
class VdmManualTransportFactoryTest extends TestCase
{
    public function testSupports()
    {
        $executors = $this->createMock(VdmManualExecutorCollection::class);
        $factory = new VdmManualTransportFactory($executors);

        $this->assertFalse($factory->supports('http://url.com', []));
        $this->assertFalse($factory->supports('   vdm+manual://url.com', []));
        $this->assertTrue($factory->supports('vdm+manual://url.com', []));
    }

    public function testCreateTransportNotFound()
    {
        $this->expectException(ServiceNotFoundException::class);

        $serializer = $this->createMock(SerializerInterface::class);
        $executors = $this->createMock(VdmManualExecutorCollection::class);
        $executors->expects($this->once())
            ->method('has')
            ->with('service_id')
            ->willReturn(false);

        $factory = new VdmManualTransportFactory($executors);
        $factory->createTransport('vdm+manual://service_id', [], $serializer);
    }

    public function testCreateTransportNotImplementInterface()
    {
        $this->expectException(TypeError::class);

        $serializer = $this->createMock(SerializerInterface::class);

        $locator = $this->createMock(ServiceLocator::class);

        $locator->expects($this->once())
            ->method('has')
            ->with('my_manual_executor')
            ->willReturn(true);

        $locator->expects($this->once())
            ->method('get')
            ->with('my_manual_executor')
            ->willReturn(new DefaultMessage());

        $executors = new VdmManualExecutorCollection($locator);

        $factory = new VdmManualTransportFactory($executors);
        $factory->createTransport('vdm+manual://my_manual_executor', [], $serializer);
    }

    public function testCreateTransport()
    {
        $serializer = $this->createMock(SerializerInterface::class);

        $locator = $this->createMock(ServiceLocator::class);

        $locator->expects($this->once())
            ->method('has')
            ->with('my_manual_executor')
            ->willReturn(true);

        $executor = new MyManualExecutor();

        $locator->expects($this->once())
            ->method('get')
            ->with('my_manual_executor')
            ->willReturn($executor);

        $executors = new VdmManualExecutorCollection($locator);

        $factory = new VdmManualTransportFactory($executors);
        $transport = $factory->createTransport('vdm+manual://my_manual_executor', [], $serializer);

        $this->assertInstanceOf(VdmManualTransport::class, $transport);

        $transport->get();

        $this->assertEquals(1, $executor->getCalled);
        $this->assertEquals(0, $executor->ackCalled);
        $this->assertEquals(0, $executor->rejectCalled);
        $this->assertEquals(0, $executor->sendCalled);

        $transport->ack(new Envelope(new \stdClass()));

        $this->assertEquals(1, $executor->getCalled);
        $this->assertEquals(1, $executor->ackCalled);
        $this->assertEquals(0, $executor->rejectCalled);
        $this->assertEquals(0, $executor->sendCalled);

        $transport->reject(new Envelope(new \stdClass()));

        $this->assertEquals(1, $executor->getCalled);
        $this->assertEquals(1, $executor->ackCalled);
        $this->assertEquals(1, $executor->rejectCalled);
        $this->assertEquals(0, $executor->sendCalled);

        $transport->send(new Envelope(new \stdClass()));

        $this->assertEquals(1, $executor->getCalled);
        $this->assertEquals(1, $executor->ackCalled);
        $this->assertEquals(1, $executor->rejectCalled);
        $this->assertEquals(1, $executor->sendCalled);
    }
}
