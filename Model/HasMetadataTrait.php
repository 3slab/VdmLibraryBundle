<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Model;

use Symfony\Component\Serializer\Annotation\Ignore;

/**
 * Trait HasMetadataTrait
 *
 * @package Vdm\Bundle\LibraryBundle\Model
 */
trait HasMetadataTrait
{
    /**
     * @var Metadata[]
     */
    protected $metadatas;

    /**
     * @return Metadata[]
     */
    public function getMetadatas(): array
    {
        return $this->metadatas;
    }

    /**
     * @Ignore()
     *
     * @param string $key
     *
     * @return Metadata[]
     */
    public function getMetadatasByKey(string $key): array
    {
        return array_filter($this->metadatas, function (Metadata $metadata) use ($key) {
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
}
