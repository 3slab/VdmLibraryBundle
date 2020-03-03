<?php

namespace Vdm\Bundle\LibraryBundle\Model;

/**
 * Class Model
 *
 * @package Vdm\Bundle\LibraryBundle\Model
 */
class Message
{
    /**
     * @var Trace[]
     */
    private $traces;

    /**
     * @var Metadata[]
     */
    private $metadatas;

    /**
     * @var string|int|float|bool|object|array|null
     */
    private $payload;

    /**
     * Model constructor.
     *
     * @param string|int|float|bool|object|array|null $payload
     * @param Metadata[] $metadatas
     * @param Trace[] $traces
     */
    public function __construct($payload, array $metadatas = [], array $traces = [])
    {
        $this->payload = $payload;
        $this->metadatas = $metadatas;
        $this->traces = $traces;
    }

    /**
     * @return string|int|float|bool|object|array|null
     */
    public function getPayload()
    {
        return $this->payload;
    }

    /**
     * @param string|int|float|bool|object|array|null $payload
     */
    public function setPayload($payload): void
    {
        $this->payload = $payload;
    }

    /**
     * @return Trace[]
     */
    public function getTraces(): array
    {
        return $this->traces;
    }

    /**
     * @param Trace[] $traces
     */
    public function setTraces(array $traces): void
    {
        $this->traces = $traces;
    }

    /**
     * @param Trace $trace
     */
    public function addTrace(Trace $trace): void
    {
        $this->traces[] = $trace;
    }

    /**
     * @return Metadata[]
     */
    public function getMetadatas(): array
    {
        return $this->metadatas;
    }

    /**
     * @param Metadata[] $metadatas
     */
    public function setMetadatas(array $metadatas): void
    {
        $this->metadatas = $metadatas;
    }

    /**
     * @param Metadata $metadata
     */
    public function addMetadata(Metadata $metadata): void
    {
        $this->metadatas[] = $metadata;
    }
}
