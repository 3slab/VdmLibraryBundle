<?php

namespace Vdm\Bundle\LibraryBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Vdm\Bundle\LibraryBundle\Client\Http\Behavior\HttpClientBehaviorFactoryRegistry;

class HttpClientBehaviorCreateCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->has(HttpClientBehaviorFactoryRegistry::class)) {
            return;
        }

        $definition = $container->findDefinition(HttpClientBehaviorFactoryRegistry::class);
        $taggedServices = $container->findTaggedServiceIds('vdm_library.http_decorator_factory');

        foreach ($taggedServices as $id => $tags) {   
            $definition->addMethodCall('addFactory', [new Reference($id), $id::priority()]);
        } 
    }
}
