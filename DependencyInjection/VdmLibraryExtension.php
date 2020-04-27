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
use Vdm\Bundle\LibraryBundle\Client\Elastic\Behavior\ElasticClientBehaviorFactoryInterface;
use Vdm\Bundle\LibraryBundle\Client\Ftp\Behavior\FtpClientBehaviorFactoryInterface;
use Vdm\Bundle\LibraryBundle\Client\Http\Behavior\HttpClientBehaviorFactoryInterface;
use Vdm\Bundle\LibraryBundle\Executor\Doctrine\AbstractDoctrineExecutor;
use Vdm\Bundle\LibraryBundle\Executor\Ftp\AbstractFtpExecutor;
use Vdm\Bundle\LibraryBundle\Executor\Http\AbstractHttpExecutor;
use Vdm\Bundle\LibraryBundle\Monitoring\StatsStorageInterface;

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
        $container->registerForAutoconfiguration(AbstractFtpExecutor::class)
            ->addTag('vdm_library.ftp_executor')
        ;
        $container->registerForAutoconfiguration(AbstractDoctrineExecutor::class)
            ->addTag('vdm_library.doctrine_executor')
        ;
        $container->registerForAutoconfiguration(FtpClientBehaviorFactoryInterface::class)
            ->addTag('vdm_library.ftp_decorator_factory')
        ;
        $container->registerForAutoconfiguration(ElasticClientBehaviorFactoryInterface::class)
            ->addTag('vdm_library.elastic_decorator_factory')
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
