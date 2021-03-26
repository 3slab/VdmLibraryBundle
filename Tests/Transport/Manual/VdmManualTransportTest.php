<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Tests\Transport\Manuel;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Vdm\Bundle\LibraryBundle\Tests\Message\DefaultMessage;
use Vdm\Bundle\LibraryBundle\Transport\Manual\VdmManualExecutorInterface;
use Vdm\Bundle\LibraryBundle\Transport\Manual\VdmManualTransport;

/**
 * Class VdmManualTransportTest
 *
 * @package Vdm\Bundle\LibraryBundle\Tests\Transport\Manuel
 */
class VdmManualTransportTest extends TestCase
{
    public function testGet()
    {
        $return = ['item1', 'item2'];
        $manualExecutorMock = $this->createMock(VdmManualExecutorInterface::class);
        $manualExecutorMock->expects($this->once())
            ->method('get')
            ->with()
            ->willReturn($return);

        $transport = new VdmManualTransport($manualExecutorMock);
        $result = $transport->get();

        $this->assertEquals($return, $result);
    }

    public function testAck()
    {
        $message = new DefaultMessage('content');
        $envelope = new Envelope($message);

        $manualExecutorMock = $this->createMock(VdmManualExecutorInterface::class);
        $manualExecutorMock->expects($this->once())
            ->method('ack')
            ->with($envelope);

        $transport = new VdmManualTransport($manualExecutorMock);
        $transport->ack($envelope);
    }

    public function testSend()
    {
        $message = new DefaultMessage('content');
        $envelope = new Envelope($message);
        $returnEnvelope = new Envelope($message);

        $manualExecutorMock = $this->createMock(VdmManualExecutorInterface::class);
        $manualExecutorMock->expects($this->once())
            ->method('send')
            ->with($envelope)
            ->willReturn($returnEnvelope);

        $transport = new VdmManualTransport($manualExecutorMock);
        $result = $transport->send($envelope);

        $this->assertEquals($returnEnvelope, $result);
    }
}
