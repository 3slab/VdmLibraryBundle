<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Tests\Service\Monitoring\Storage;

use DataDog\BatchedDogStatsd;
use DataDog\DogStatsd;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Vdm\Bundle\LibraryBundle\Service\Monitoring\Storage\StatsDStorage;

/**
 * Class StatsDStorageTest
 *
 * @package Vdm\Bundle\LibraryBundle\Tests\Service\Monitoring\Storage
 */
class StatsDStorageTest extends TestCase
{
    public function accessProtected($obj, $prop) {
        $reflection = new ReflectionClass($obj);
        $property = $reflection->getProperty($prop);
        $property->setAccessible(true);
        return $property->getValue($obj);
    }

    public function testDatadogClientInstanceType()
    {
        $statsd = new StatsDStorage(['batched' => 1], 'myapp');
        $datadog = $this->accessProtected($statsd, 'datadog');
        $this->assertInstanceOf(BatchedDogStatsd::class, $datadog);

        $statsd = new StatsDStorage([], 'myapp');
        $datadog = $this->accessProtected($statsd, 'datadog');
        $this->assertInstanceOf(DogStatsd::class, $datadog);
    }

    public function testPrepareConfig()
    {
        $config = [
            'host' => 'myhost',
            'port' => 1234,
            'unknown' => 'value'
        ];
        $statsd = new StatsDStorage($config, 'myapp');
        $preparedConfig = $this->accessProtected($statsd, 'config');
        $this->assertEquals(
            [
                'host' => 'myhost',
                'port' => 1234,
                'batched' => false,
                'global_tags' => [
                    'appName' => 'myapp'
                ],
                'unknown' => 'value'
            ],
            $preparedConfig
        );
    }
}