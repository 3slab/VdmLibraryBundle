<?php

namespace App\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 *
 * @package App\DependencyInjection
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('vdm_dataflow');

        $treeBuilder->getRootNode()
            ->children()
                ->scalarNode('app_name')->defaultValue('default')->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
