<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Service\Monitoring\Storage;

use Psr\Log\NullLogger;
use Psr\Log\LoggerInterface;
use Vdm\Bundle\LibraryBundle\Service\Monitoring\Storage\StorageInterface;

class NullStorage implements StorageInterface
{
    /**
     * @var LoggerInterface $logger
     */
    protected $logger;

    /**
     * @var array
     */
    private $config;

    /**
     * @var string
     */
    private $appName;

    /**
     * NullStorage constructor.
     *
     * @param array $config
     * @param string $appName
     * @param LoggerInterface|null $vdmLogger
     */
    public function __construct(array $config, string $appName, LoggerInterface $vdmLogger = null)
    {
        $this->config = $config;
        $this->appName = $appName;
        $this->logger = $vdmLogger ?? new NullLogger();
    }

    /**
     * {@inheritDoc}
     */
    public static function getType(): string
    {
        return 'null';
    }

    /**
     * {@inheritDoc}
     */
    public function increment(string $key, int $value): void
    {
    }

    /**
     * {@inheritDoc}
     */
    public function update(string $key, $value): void
    {
    }

    /**
     * {@inheritDoc}
     */
    public function flush(): void
    {
    }
}
