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
use Vdm\Bundle\LibraryBundle\Service\Monitoring\Storage\StorageFactory;
use Vdm\Bundle\LibraryBundle\Service\Monitoring\Storage\StorageInterface;

/**
 * Class SetStorageCompilerPass
 *
 * @package Vdm\Bundle\LibraryBundle\DependencyInjection\Compiler
 */
class SetStorageCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {
        $monitoringType = $container->getParameter('vdm_library.monitoring_type');
        $monitoringOptions = $container->getParameter('vdm_library.monitoring_options');
        $appName = $container->getParameter('vdm_library.app_name');

        // Get reference to the storage factory service
        $factoryDefinition = $container->findDefinition(StorageFactory::class);

        // Register all available storage class in the factory
        $storageClasses = $container->findTaggedServiceIds('vdm_library.monitoring.storage_class');
        foreach ($storageClasses as $class => $tags) {
            $factoryDefinition->addMethodCall('registerStorageClass', [$class]);
        }

        // Create the service definition for the storage based on bundle configuration
        $storageDefinition = new Definition(StorageInterface::class);
        $storageDefinition->setFactory([$factoryDefinition, 'build']);
        $storageDefinition->setArguments([
            '$type' => $monitoringType,
            '$options' => $monitoringOptions,
            '$appName' => $appName
        ]);

        $container->setDefinition('vdm_library.monitoring.storage', $storageDefinition);
        $container->setAlias(StorageInterface::class, 'vdm_library.monitoring.storage');
    }
}
