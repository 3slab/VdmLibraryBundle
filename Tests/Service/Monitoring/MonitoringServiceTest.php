<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Tests\Service\Monitoring;

use PHPUnit\Framework\TestCase;
use Vdm\Bundle\LibraryBundle\Service\Monitoring\MonitoringService;
use Vdm\Bundle\LibraryBundle\Service\Monitoring\Storage\StorageInterface;

/**
 * Class MonitoringServiceTest
 *
 * @package Vdm\Bundle\LibraryBundle\Tests\Service\Monitoring
 */
class MonitoringServiceTest extends TestCase
{
    public function testUpdate()
    {
        $key = 'key';
        $value = 'value';
        $tags = ['key' => 'value'];

        $storage = $this->createMock(StorageInterface::class);
        $storage->expects($this->once())
            ->method('update')
            ->with($key, $value, $tags);

        $monitoring = new MonitoringService($storage);
        $monitoring->update($key, $value, $tags);
    }

    public function testIncrement()
    {
        $key = 'key';
        $value = 2;
        $tags = ['key' => 'value'];

        $storage = $this->createMock(StorageInterface::class);
        $storage->expects($this->once())
            ->method('increment')
            ->with($key, $value, $tags);

        $monitoring = new MonitoringService($storage);
        $monitoring->increment($key, $value, $tags);
    }

    public function testFlush()
    {
        $storage = $this->createMock(StorageInterface::class);
        $storage->expects($this->once())
            ->method('flush')
            ->with();

        $monitoring = new MonitoringService($storage);
        $monitoring->flush();
    }
}
