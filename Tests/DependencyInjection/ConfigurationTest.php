<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Processor;
use Vdm\Bundle\LibraryBundle\DependencyInjection\Configuration;

class ConfigurationTest extends TestCase
{
    /**
     * @var Processor
     */
    protected $processor;

    public function setUp(): void
    {
        $this->processor = new Processor();
    }

    public function testEmptyConfig(): void
    {
        $configuration = new Configuration();
        $config = $this->processor->processConfiguration($configuration, []);

        $this->assertEquals(
            [
                'app_name' => 'default',
                'monitoring' => [
                    'type' => 'null',
                    'options' => []
                ]
            ],
            $config
        );
    }

    public function testValidConfig(): void
    {
        $unprocessedConfig = [
            'vdm_library' => [
                'app_name' => 'myapp',
                'monitoring' => [
                    'type' => 'statsd',
                    'options' => [
                        'key1' => 'value1',
                        'key2' => 'value2'
                    ]
                ]
            ]
        ];

        $configuration = new Configuration();
        $config = $this->processor->processConfiguration(
            $configuration,
            $unprocessedConfig
        );

        $this->assertEquals($unprocessedConfig['vdm_library'], $config);
    }
}
