<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Tests\Fixtures\AppBundle\Service\Monitoring\Storage;

use Psr\Log\LoggerInterface;
use Vdm\Bundle\LibraryBundle\Service\Monitoring\Storage\StorageInterface;

/**
 * Class TestStorage
 *
 * @package Vdm\Bundle\LibraryBundle\Tests\Service\Monitoring\Storage
 */
class TestStorage implements StorageInterface
{
    /**
     * @var array
     */
    public $config;

    /**
     * @var string
     */
    public $appName;

    /**
     * TestStorage constructor.
     *
     * @param array $config
     * @param string $appName
     * @param LoggerInterface|null $vdmLogger
     */
    public function __construct(array $config, string $appName, LoggerInterface $vdmLogger = null)
    {
        $this->config = $config;
        $this->appName = $appName;
    }

    /**
     * {@inheritDoc}
     */
    public static function getType(): string
    {
        return 'test';
    }

    /**
     * {@inheritDoc}
     */
    public function increment(string $key, int $value, array $tags = null): void
    {
    }

    /**
     * {@inheritDoc}
     */
    public function update(string $key, $value, array $tags = null): void
    {
    }

    /**
     * {@inheritDoc}
     */
    public function flush(): void
    {
    }
}
