<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Model;

use Symfony\Component\Serializer\Annotation\Ignore;

/**
 * Interface HasMetadataMessageInterface
 *
 * @package Vdm\Bundle\LibraryBundle\Model
 */
interface HasMetadataMessageInterface
{
    /**
     * @return Metadata[]
     */
    public function getMetadatas(): array;

    /**
     * @Ignore()
     *
     * @param string $key
     *
     * @return Metadata[]
     */
    public function getMetadatasByKey(string $key): array;

    /**
     * @param Metadata[] $metadatas
     */
    public function setMetadatas(array $metadatas): void;

    /**
     * @param Metadata $metadata
     */
    public function addMetadata(Metadata $metadata): void;
}
