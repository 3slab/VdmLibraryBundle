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
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('vdm_library');
        if (method_exists(TreeBuilder::class, 'getRootNode')) {
            $rootNode = $treeBuilder->getRootNode();
        } else {
            $rootNode = $treeBuilder->root('vdm_library');
        }

        $rootNode
            ->children()
                ->scalarNode('app_name')->defaultValue('default')->end()
                ->booleanNode('print_msg')->defaultFalse()->end()
                ->booleanNode('stop_on_error')->defaultTrue()->end()
                ->arrayNode('monitoring')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('type')->treatNullLike('null')->defaultValue('null')->end()
                        ->variableNode('options')->defaultValue([])->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
