<?php

namespace Vdm\Bundle\LibraryBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 *
 * @package Vdm\Bundle\LibraryBundle\DependencyInjection
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('vdm_library');

        $treeBuilder->getRootNode()
            ->children()
                ->scalarNode('app_name')->defaultValue('default')->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
