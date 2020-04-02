<?php

namespace Vdm\Bundle\LibraryBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Vdm\Bundle\LibraryBundle\Client\Elastic\Behavior\ElasticClientBehaviorFactoryRegistry;

class ElasticClientBehaviorCreateCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->has(ElasticClientBehaviorFactoryRegistry::class)) {
            return;
        }

        $definition = $container->findDefinition(ElasticClientBehaviorFactoryRegistry::class);
        $taggedServices = $container->findTaggedServiceIds('vdm_library.es_decorator_factory');

        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall('addFactory', [new Reference($id), $id::priority()]);
        }
    }
}
