<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Vdm\Bundle\LibraryBundle\Service\Monitoring\Storage\StorageInterface;

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
        $container
            ->registerForAutoconfiguration(StorageInterface::class)
            ->addTag('vdm_library.monitoring.storage_class')
        ;

        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );
        $loader->load('services.yaml');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('vdm_library.app_name', $config['app_name']);
        $container->setParameter('vdm_library.print_msg', $config['print_msg']);
        $container->setParameter('vdm_library.stop_on_error', $config['stop_on_error']);
        $container->setParameter('vdm_library.monitoring_type', $config['monitoring']['type']);
        $container->setParameter('vdm_library.monitoring_options', $config['monitoring']['options']);
    }

    /**
     * {@inheritDoc}
     */
    public function getAlias(): string
    {
        return 'vdm_library';
    }
}
