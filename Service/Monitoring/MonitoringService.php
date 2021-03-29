<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Service\Monitoring;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
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
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * MonitoringService constructor.
     *
     * @param StorageInterface $storage
     * @param LoggerInterface|null $vdmLogger
     */
    public function __construct(StorageInterface $storage, LoggerInterface $vdmLogger = null)
    {
        $this->storage = $storage;
        $this->logger = $vdmLogger ?? new NullLogger();
    }

    /**
     * Update a value
     *
     * @param string $key
     * @param $value
     */
    public function update(string $key, $value): void
    {
        try {
            $this->storage->update($key, $value);
        } catch (\Exception $e) {
            $this->logger->error("error occurred when updating $key metric", [
                'exception' => $e
            ]);
        }
    }

    /**
     * Increment a value
     *
     * @param string $key
     * @param int $value
     */
    public function increment(string $key, int $value): void
    {
        try {
            $this->storage->increment($key, $value);
        } catch (\Exception $e) {
            $this->logger->error("error occurred when incrementing $key metric", [
                'exception' => $e
            ]);
        }
    }

    /**
     * Flush storage
     */
    public function flush(): void
    {
        try {
            $this->storage->flush();
        } catch (\Exception $e) {
            $this->logger->error("error occurred when flushing metrics", [
                'exception' => $e
            ]);
        }
    }
}