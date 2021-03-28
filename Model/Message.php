<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Model;

use Symfony\Component\Serializer\Annotation\Ignore;

/**
 * Class Model
 *
 * @package Vdm\Bundle\LibraryBundle\Model
 */
abstract class Message implements HasMetadataMessageInterface, IsEmptyMessageInterface, TraceableMessageInterface
{
    use HasMetadataTrait;
    use TraceableTrait;

    /**
     * @var string|int|float|bool|array|null
     */
    protected $payload;

    /**
     * Model constructor.
     *
     * @param string|int|float|bool|array|null $payload
     * @param Metadata[] $metadatas
     * @param Trace[] $traces
     */
    public function __construct($payload = null, array $metadatas = [], array $traces = [])
    {
        $this->payload = $payload;
        $this->metadatas = $metadatas;
        $this->traces = $traces;
    }

    /**
     * @return string|int|float|bool|array|null
     */
    public function getPayload()
    {
        return $this->payload;
    }

    /**
     * @param string|int|float|bool|array|null $payload
     */
    public function setPayload($payload): void
    {
        $this->payload = $payload;
    }

    /**
     * @Ignore()
     * {@inheritDoc}
     */
    public function isEmpty(): bool
    {
        return empty($this->getPayload());
    }

    /**
     * @param Message $message
     * @return Message
     */
    public static function createFrom(Message $message): Message
    {
        return new static($message->getPayload(), $message->getMetadatas(), $message->getTraces());
    }
}
