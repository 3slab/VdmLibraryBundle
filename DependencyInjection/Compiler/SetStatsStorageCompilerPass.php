<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
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

        foreach ($taggedServices as $class => $tags) {
            if ($class::getType() === $monitoringType) {
                $container->setAlias(StatsStorageInterface::class, $class);

                $definition = $container->getDefinition($class);
                $definition->setArguments(['$config' => $monitoringOptions, '$appName' => $appName]);

                return;
            }
        }

        throw new InvalidArgumentException(sprintf('No implementation found for stat storage type %s', $monitoringType));
    }
}
