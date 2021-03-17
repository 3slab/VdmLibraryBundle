<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Model;

/**
 * Class Model
 *
 * @package Vdm\Bundle\LibraryBundle\Model
 */
abstract class Message implements IsEmptyMessageInterface, TraceableMessageInterface
{
    use TraceableTrait;

    /**
     * @var Metadata[]
     */
    private $metadatas;

    /**
     * @var string|int|float|bool|array|null
     */
    private $payload;

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
     * @return Metadata[]
     */
    public function getMetadatas(): array
    {
        return $this->metadatas;
    }

    /**
     * @param string $key
     *
     * @return Metadata[]
     */
    public function getMetadatasByKey(string $key): array
    {
        return array_filter($this->metadatas, function(Metadata $metadata) use ($key) {
            return $metadata->getKey() === $key;
        });
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

    /**
     * {@inheritDoc}
     */
    public function isEmpty(): bool
    {
        return empty($this->getPayload());
    }
}
