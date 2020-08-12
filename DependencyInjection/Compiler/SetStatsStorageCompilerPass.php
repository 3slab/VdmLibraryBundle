<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Vdm\Bundle\LibraryBundle\Monitoring\StatsStorageFactory;
use Vdm\Bundle\LibraryBundle\Monitoring\StatsStorageInterface;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;

class SetStatsStorageCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $monitoringType = $container->getParameter('vdm_library.monitoring_type');
        $monitoringOptions = $container->getParameter('vdm_library.monitoring_options');
        $appName = $container->getParameter('vdm_library.app_name');

        $taggedServices = $container->findTaggedServiceIds('vdm_library.stats_storage');

        $definition = $container->findDefinition(StatsStorageFactory::class);

        foreach ($taggedServices as $class => $tags) {
            $definition->addMethodCall('registerStatStorageClass', [$class]);
        }
        $storage = new Definition(StatsStorageInterface::class);
        $storage->setFactory([$definition, 'build']);
        $storage->setArguments(['$type' => $monitoringType, '$options' => $monitoringOptions, '$appName' => $appName]);

        $container->setDefinition('vdm_library.stats.storage', $storage);
        $container->setAlias(StatsStorageInterface::class, 'vdm_library.stats.storage');
    }
}
