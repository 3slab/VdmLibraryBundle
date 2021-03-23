<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Service\Monitoring;

use Vdm\Bundle\LibraryBundle\Service\Monitoring\Storage\StorageInterface;

/**
 * Class MonitoringService
 *
 * @package Vdm\Bundle\LibraryBundle\Service\Monitoring
 */
class MonitoringService
{
    /**
     * @var StorageInterface
     */
    protected $storage;

    /**
     * MonitoringService constructor.
     *
     * @param StorageInterface $storage
     */
    public function __construct(StorageInterface $storage)
    {
        $this->storage = $storage;
    }

    /**
     * Update a value
     *
     * @param string $key
     * @param $value
     */
    public function update(string $key, $value): void
    {
        $this->storage->update($key, $value);
    }

    /**
     * Increment a value
     *
     * @param string $key
     * @param int $value
     */
    public function increment(string $key, int $value): void
    {
        $this->storage->increment($key, $value);
    }

    /**
     * Flush storage
     */
    public function flush(): void
    {
        $this->storage->flush();
    }
}