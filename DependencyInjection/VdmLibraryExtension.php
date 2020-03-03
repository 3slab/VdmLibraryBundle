<?php

namespace Vdm\Bundle\LibraryBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;

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
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        $container->setParameter('vdm_library.app_name', $config['app_name']);
        
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../Resources/config')
        );
        
        $loader->load('services.yaml');
    }

    /**
     * {@inheritDoc}
     */
    public function getAlias()
    {
        return 'vdm_library';
    }
}
