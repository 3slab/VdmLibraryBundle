<?php

/**
 * @package    3slab/VdmLibraryDoctrineOrmTransportBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryDoctrineOrmTransportBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Tests\Transport\Manual;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Vdm\Bundle\LibraryBundle\Transport\Manual\VdmManualExecutorCollection;
use Vdm\Bundle\LibraryBundle\Transport\Manual\VdmManualExecutorInterface;

/**
 * Class VdmManualExecutorCollectionTest
 *
 * @package Vdm\Bundle\LibraryBundle\Tests\Transport\Manual
 */
class VdmManualExecutorCollectionTest extends TestCase
{
    public function testGet()
    {
        $return = $this->createMock(VdmManualExecutorInterface::class);
        $locatorMock = $this->createMock(ServiceLocator::class);
        $locatorMock->expects($this->once())
            ->method('get')
            ->with('service_id')
            ->willReturn($return);

        $transport = new VdmManualExecutorCollection($locatorMock);
        $result = $transport->get('service_id');

        $this->assertEquals($return, $result);
    }

    public function testHas()
    {
        $locatorMock = $this->createMock(ServiceLocator::class);
        $locatorMock->expects($this->once())
            ->method('has')
            ->with('service_id')
            ->willReturn(true);

        $transport = new VdmManualExecutorCollection($locatorMock);
        $result = $transport->has('service_id');

        $this->assertTrue($result);
    }
}
