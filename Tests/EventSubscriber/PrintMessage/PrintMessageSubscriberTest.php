<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Tests\EventSubscriber\PrintMessage;

use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Event\SendMessageToTransportsEvent;
use Symfony\Component\Messenger\Event\WorkerMessageReceivedEvent;
use Symfony\Component\Messenger\MessageBus;
use Symfony\Component\VarDumper\VarDumper;
use Vdm\Bundle\LibraryBundle\EventSubscriber\PrintMessage\PrintMessageSubscriber;

/**
 * Class PrintMessageSubscriber
 * @package Vdm\Bundle\LibraryBundle\EventSubscriber\PrintMessage
 */
class PrintMessageSubscriberTest extends TestCase
{
    protected $previousHandler;

    protected $varDumperArg = null;

    public function setUp(): void
    {
        parent::setUp();
        $this->previousHandler = VarDumper::setHandler([$this, 'varDumper']);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        VarDumper::setHandler($this->previousHandler);
        $this->varDumperArg = null;
    }

    public function varDumper($var)
    {
        $this->varDumperArg = $var;
    }

    public function testOnWorkerMessageReceivedEventPrintDisabled()
    {
        $this->setOutputCallback(function() {});

        $message = new \stdClass();
        $envelope = new Envelope($message);

        $subscriber = new PrintMessageSubscriber();
        $subscriber->onWorkerMessageReceivedEvent(new WorkerMessageReceivedEvent($envelope, 'transport'));

        $this->assertNull($this->varDumperArg);
    }

    public function testOnWorkerMessageReceivedEventPrintEnabled()
    {
        $this->setOutputCallback(function() {});

        $message = new \stdClass();
        $envelope = new Envelope($message);

        $subscriber = new PrintMessageSubscriber(true);
        $subscriber->onWorkerMessageReceivedEvent(new WorkerMessageReceivedEvent($envelope, 'transport'));

        $this->assertEquals($message, $this->varDumperArg);
    }

    public function testOnSendMessageToTransportEventPrintDisabled()
    {
        $this->setOutputCallback(function() {});

        $message = new \stdClass();
        $envelope = new Envelope($message);

        $subscriber = new PrintMessageSubscriber();
        $subscriber->onSendMessageToTransportEvent(new SendMessageToTransportsEvent($envelope));

        $this->assertNull($this->varDumperArg);
    }

    public function testOnSendMessageToTransportEventPrintEnabled()
    {
        $this->setOutputCallback(function() {});

        $message = new \stdClass();
        $envelope = new Envelope($message);

        $subscriber = new PrintMessageSubscriber(true);
        $subscriber->onSendMessageToTransportEvent(new SendMessageToTransportsEvent($envelope));

        $this->assertEquals($message, $this->varDumperArg);
    }
}
