<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Tests\Service;

use PHPUnit\Framework\TestCase;
use Vdm\Bundle\LibraryBundle\Service\StopWorkerService;

/**
 * Class StopWorkerServiceTest
 *
 * @package Vdm\Bundle\LibraryBundle\Tests\Service
 */
class StopWorkerServiceTest extends TestCase
{
    public function testGetterSetterFlag()
    {
        $service = new StopWorkerService();

        $this->assertFalse($service->getFlag());
        $this->assertNull($service->getThrowable());

        $service->setFlag(true);

        $this->assertTrue($service->getFlag());
        $this->assertNull($service->getThrowable());

        $exception = new \Exception('exception');
        $service->setThrowable($exception);

        $this->assertTrue($service->getFlag());
        $this->assertEquals($exception, $service->getThrowable());
    }
}
