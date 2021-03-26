<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 *
 * @noinspection PhpVoidFunctionResultUsedInspection
 */

namespace Vdm\Bundle\LibraryBundle\Tests\DependencyInjection;

use Vdm\Bundle\LibraryBundle\Service\Monitoring\MonitoringService;
use Vdm\Bundle\LibraryBundle\Service\Monitoring\Storage\StorageFactory;
use Vdm\Bundle\LibraryBundle\Tests\LibraryKernelTestCase;

class VdmLibraryExtensionTest extends LibraryKernelTestCase
{
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * {@inheritDoc}
     */
    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * {@inheritDoc}
     */
    protected static function getAppName(): string
    {
        return 'app1';
    }

    public function testKernel()
    {
        $container = static::$kernel->getContainer();

        $this->assertEquals('myapptest', $container->getParameter('vdm_library.app_name'));
        $this->assertEquals('test', $container->getParameter('vdm_library.monitoring_type'));
        $this->assertEquals(['myoptions'], $container->getParameter('vdm_library.monitoring_options'));
    }
}
