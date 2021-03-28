<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Tests\Model;

use PHPUnit\Framework\TestCase;
use Vdm\Bundle\LibraryBundle\Model\Metadata;
use Vdm\Bundle\LibraryBundle\Model\Trace;
use Vdm\Bundle\LibraryBundle\Tests\Fixtures\AppBundle\Message\AnotherMessage;
use Vdm\Bundle\LibraryBundle\Tests\Fixtures\AppBundle\Message\DefaultMessage;

class MessageTest extends TestCase
{
    public function testPayload()
    {
        $message = new DefaultMessage();
        $this->assertNull($message->getPayload());
        $message->setPayload('mypayload');
        $this->assertEquals('mypayload', $message->getPayload());
        $message->setPayload(1);
        $this->assertEquals(1, $message->getPayload());
        $message->setPayload(1.1);
        $this->assertEquals(1.1, $message->getPayload());
        $message->setPayload(true);
        $this->assertTrue($message->getPayload());
        $message->setPayload(['key' => 'value']);
        $this->assertEquals(['key' => 'value'], $message->getPayload());
        $message->setPayload(null);
        $this->assertNull($message->getPayload());
    }

    public function testMetadatas()
    {
        $message = new DefaultMessage();
        $this->assertCount(0, $message->getMetadatas());
        $this->assertEquals([], $message->getMetadatasByKey('key1'));
        $metadata1 = new Metadata('key1', 'value');
        $message->addMetadata($metadata1);
        $this->assertCount(1, $message->getMetadatas());
        $this->assertEquals([$metadata1], $message->getMetadatasByKey('key1'));
        $metadata2 = new Metadata('key2', 'value');
        $message->addMetadata($metadata2);
        $this->assertCount(2, $message->getMetadatas());
        $this->assertEquals([$metadata1, $metadata2], $message->getMetadatas());
        $this->assertEquals([$metadata1], $message->getMetadatasByKey('key1'));
        $message->setMetadatas([$metadata1]);
        $this->assertCount(1, $message->getMetadatas());
        $this->assertEquals([$metadata1], $message->getMetadatas());
        $this->assertEquals([$metadata1], $message->getMetadatasByKey('key1'));
    }

    public function testTraces()
    {
        $message = new DefaultMessage();
        $this->assertCount(0, $message->getTraces());
        $this->assertNull($message->getLastTrace());
        $trace1 = new Trace('trace', 'event');
        $message->addTrace($trace1);
        $this->assertCount(1, $message->getTraces());
        $this->assertEquals([$trace1], $message->getTraces());
        $this->assertEquals($trace1, $message->getLastTrace());
        $trace2 = new Trace('trace', 'event');
        $message->addTrace($trace2);
        $this->assertCount(2, $message->getTraces());
        $this->assertEquals([$trace1, $trace2], $message->getTraces());
        $this->assertEquals($trace2, $message->getLastTrace());
        $message->setTraces([$trace1]);
        $this->assertCount(1, $message->getTraces());
        $this->assertEquals([$trace1], $message->getTraces());
        $this->assertEquals($trace1, $message->getLastTrace());
    }

    public function testIsEmpty()
    {
        $message = new DefaultMessage();
        $this->assertTrue($message->isEmpty());
        $message->setPayload('mypayload');
        $this->assertFalse($message->isEmpty());
    }

    public function testCreateFrom()
    {
        $payload = 'mypayload';
        $metadatas = [new Metadata('key', 'value')];
        $traces = [new Trace('name', 'event')];
        $default = new DefaultMessage($payload, $metadatas, $traces);
        $message = AnotherMessage::createFrom($default);

        $this->assertInstanceOf(AnotherMessage::class, $message);
        $this->assertEquals($payload, $message->getPayload());
        $this->assertEquals($metadatas, $message->getMetadatas());
        $this->assertEquals($traces, $message->getTraces());
    }
}