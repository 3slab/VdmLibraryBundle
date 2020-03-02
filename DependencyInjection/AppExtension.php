<?php

namespace App\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;

/**
 * Class AppExtension
 *
 * @package App\DependencyInjection
 */
class AppExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('vdm_dataflow.app_name', $config['app_name']);
    }

    /**
     * {@inheritDoc}
     */
    public function getAlias()
    {
        return 'vdm_dataflow';
    }
}
