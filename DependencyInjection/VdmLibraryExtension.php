<?php

namespace Vdm\Bundle\LibraryBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;
use Vdm\Bundle\LibraryBundle\Monitoring\StatsStorageInterface;
use Vdm\Bundle\LibraryBundle\RequestExecutor\HttpRequestExecutorInterface;

/**
 * Class VdmLibraryExtension
 *
 * @package Vdm\Bundle\LibraryBundle\DependencyInjection
 */
class VdmLibraryExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $container->registerForAutoconfiguration(StatsStorageInterface::class)
            ->addTag('vdm_library.stats_storage')
        ;
        $container->registerForAutoconfiguration(HttpRequestExecutorInterface::class)
            ->addTag('vdm_library.request_executor')
        ;

        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../Resources/config')
        );
        $loader->load('services.yaml');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('vdm_library.app_name', $config['app_name']);
        $container->setParameter('vdm_library.monitoring_type', $config['monitoring']['type']);
        $container->setParameter('vdm_library.monitoring_options', $config['monitoring']['options']);
    }

    /**
     * {@inheritDoc}
     */
    public function getAlias()
    {
        return 'vdm_library';
    }
}
