<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Tests\Service\Monitoring;

use PHPUnit\Framework\TestCase;
use Vdm\Bundle\LibraryBundle\Service\Monitoring\Storage\NullStorage;
use Vdm\Bundle\LibraryBundle\Service\Monitoring\Storage\StatsDStorage;
use Vdm\Bundle\LibraryBundle\Service\Monitoring\Storage\StorageFactory;
use Vdm\Bundle\LibraryBundle\Tests\Fixtures\AppBundle\Message\DefaultMessage;
use Vdm\Bundle\LibraryBundle\Tests\Fixtures\AppBundle\Service\Monitoring\Storage\TestStorage;

/**
 * Class StorageFactoryTest
 *
 * @package Vdm\Bundle\LibraryBundle\Tests\Service\Monitoring
 */
class StorageFactoryTest extends TestCase
{
    public function testFactory()
    {
        $factory = new StorageFactory();

        $factory->registerStorageClass(DefaultMessage::class);
        $result = $factory->build('other', [], 'myapp');
        $this->assertInstanceOf(NullStorage::class, $result);

        $factory->registerStorageClass(StatsDStorage::class);
        $result = $factory->build('statsd', [], 'myapp');
        $this->assertInstanceOf(StatsDStorage::class, $result);

        $factory->registerStorageClass(TestStorage::class);
        $config = ['host' => 'localhost'];
        $appName = 'myvdmapp';
        $result = $factory->build('test', $config, $appName);
        $this->assertInstanceOf(TestStorage::class, $result);
        $this->assertEquals($config, $result->config);
        $this->assertEquals($appName, $result->appName);
    }
}
