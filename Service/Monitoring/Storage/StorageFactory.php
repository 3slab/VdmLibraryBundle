<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Service\Monitoring\Storage;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * Class StorageFactory
 *
 * @package Vdm\Bundle\LibraryBundle\Service\Monitoring\Storage
 */
class StorageFactory
{
    /**
     * @var StorageInterface[]
     */
    protected $storageClasses = [];

    /**
     * @var LoggerInterface $logger
     */
    protected $logger;

    /**
     * StorageFactory constructor.
     *
     * @param LoggerInterface|null $vdmLogger
     */
    public function __construct(LoggerInterface $vdmLogger = null)
    {
        $this->logger = $vdmLogger ?? new NullLogger();
    }

    /**
     * Register an available storage class in the factory
     *
     * @param string $class
     */
    public function registerStorageClass(string $class): void
    {
        // Useless if, just for typehint in IDE.
        if (method_exists($class, 'getType')) {
            $this->storageClasses[$class::getType()] = $class;
        }
    }

    /**
     * Instantiate a monitoring storage
     *
     * @param string $type
     * @param array $options
     * @param string $appName
     *
     * @return StorageInterface
     */
    public function build(string $type, array $options, string $appName): StorageInterface
    {
        if (array_key_exists($type, $this->storageClasses)) {
            return new $this->storageClasses[$type]($options, $appName, $this->logger);
        }

        $this->logger->warning("monitoring storage $type does not exist. Fallback to null storage");
        return new NullStorage($options, $appName, $this->logger);
    }
}
