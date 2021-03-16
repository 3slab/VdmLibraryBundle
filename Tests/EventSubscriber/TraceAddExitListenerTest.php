<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Tests\EventSubscriber;

use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Event\SendMessageToTransportsEvent;
use Vdm\Bundle\LibraryBundle\EventSubscriber\TraceAddExitListener;
use Vdm\Bundle\LibraryBundle\Model\Message;

class TraceAddExitListenerTest extends TestCase
{
    public function testOnSendMessageToTransport()
    {
        $envelope = new Envelope(new \stdClass());
        $event = new SendMessageToTransportsEvent($envelope);

        $listener = new TraceAddExitListener('', new NullLogger());
        $result = $listener->onSendMessageToTransport($event);

        $this->assertNull($result);
    }

    public function testOnSendMessageToTransportAdd()
    {
        $envelope = new Envelope(new Message(''));
        $event = new SendMessageToTransportsEvent($envelope);
        
        $logger = $this->getMockBuilder(LoggerInterface::class)->getMock();
        $logger->expects($this->once())->method('info');

        $listener = new TraceAddExitListener('', $logger);
        $listener->onSendMessageToTransport($event);
    }
}
