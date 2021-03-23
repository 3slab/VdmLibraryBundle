<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Service\Monitoring\Storage;

/**
 * Interface StatsStorageInterface
 *
 * @package Vdm\Bundle\LibraryBundle\Service\Monitoring\Storage
 */
interface StorageInterface
{
    /**
     * Return the type of storage (as a code)
     *
     * @return string
     */
    public static function getType(): string;

    /**
     * Increment the value of a metric
     *
     * @param string $key
     * @param int $value
     */
    public function increment(string $key, int $value): void;

    /**
     * Update the value of a metric
     *
     * @param string $key
     * @param $value
     */
    public function update(string $key, $value): void;

    /**
     * Flush storage
     */
    public function flush(): void;
}
