<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Service\Monitoring\Storage;

use DataDog\BatchedDogStatsd;
use DataDog\DogStatsd;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * Class StatsDStorage
 *
 * @package Vdm\Bundle\LibraryBundle\Service\Monitoring\Storage
 */
class StatsDStorage implements StorageInterface
{
    /**
     * @var LoggerInterface $logger
     */
    protected $logger;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var DogStatsd
     */
    protected $datadog;

    /**
     * @var string
     */
    protected $appName;

    /**
     * StatsDStorage constructor.
     *
     * @param array $config
     * @param string $appName
     * @param LoggerInterface|null $vdmLogger
     */
    public function __construct(array $config, string $appName, LoggerInterface $vdmLogger = null)
    {
        if (false === class_exists(DogStatsd::class)) {
            throw new \LogicException(
                'Seems client library is not installed. Please install "datadog/php-datadogstatsd"'
            );
        }

        $this->appName = $appName;
        $this->config = $this->prepareConfig($config);
        $this->logger = $vdmLogger ?? new NullLogger();

        if (null === $this->datadog) {
            if (true === filter_var($this->config['batched'], FILTER_VALIDATE_BOOLEAN)) {
                $this->datadog = new BatchedDogStatsd($this->config);
            } else {
                $this->datadog = new DogStatsd($this->config);
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function increment(string $key, int $value, array $tags = null): void
    {
        $this->datadog->increment($key, 1.0, $tags, $value);
    }

    /**
     * {@inheritDoc}
     */
    public function update(string $key, $value, array $tags = null): void
    {
        $this->datadog->gauge($key, $value, 1.0, $tags);
    }

    /**
     * {@inheritDoc}
     */
    public function flush(): void
    {
        if (true === filter_var($this->config['batched'], FILTER_VALIDATE_BOOLEAN)) {
            $this->datadog->flushBuffer();
        }
    }

    /**
     * @param array $config
     *
     * @return array
     */
    protected function prepareConfig(array $config): array
    {
        $config['global_tags'] = [
            'appName' => $this->appName,
        ];

        return array_replace([
            'host' => 'localhost',
            'port' => 9125,
            'batched' => false,
            'global_tags' => [],
        ], $config);
    }

    /**
     * {@inheritDoc}
     */
    public static function getType(): string
    {
        return 'statsd';
    }
}
