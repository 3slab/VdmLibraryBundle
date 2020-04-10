<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

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
        $rootNode = method_exists(TreeBuilder::class, 'getRootNode') ? $treeBuilder->getRootNode() : $treeBuilder->root('vdm_library');

        $rootNode
            ->children()
                ->scalarNode('app_name')->defaultValue('default')->end()
                ->arrayNode('monitoring')
                    ->children()
                        ->scalarNode('type')->treatNullLike('null')->defaultValue('null')->end()
                        ->variableNode('options')->end()
                    ->end()
                ->end()
                ->append($this->addDoctrineNode())
            ->end()
        ;

        return $treeBuilder;
    }

    public function addDoctrineNode()
    {
        $treeBuilder = new TreeBuilder('doctrine');

        $node = $treeBuilder->getRootNode()
            ->treatNullLike([])
            ->children()
                ->scalarNode('connection')->defaultValue('default')->end()
                ->arrayNode('nullable_fields_whitelist')
                    ->isRequired()
                    ->useAttributeAsKey('entity')
                    ->arrayPrototype()
                        ->scalarPrototype()->end()
                    ->end()
                ->end()
            ->end()
        ;

    return $node;
    }
}
