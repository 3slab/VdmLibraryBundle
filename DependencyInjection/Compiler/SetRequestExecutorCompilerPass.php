<?php

namespace Vdm\Bundle\LibraryBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Vdm\Bundle\LibraryBundle\RequestExecutor\DefaultRequestExecutor;
use Vdm\Bundle\LibraryBundle\Transport\HttpTransportFactory;

class SetRequestExecutorCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->has(HttpTransportFactory::class)) {
            return;
        }

        $taggedServicesRequestExecutor = $container->findTaggedServiceIds('vdm_library.request_executor');

        // Unload default request executor if multiple requestExecutor
        if (count($taggedServicesRequestExecutor) > 1) {
            foreach ($taggedServicesRequestExecutor as $id => $tags) {
                if ($id === DefaultRequestExecutor::class) {
                    unset($taggedServicesRequestExecutor[$id]);
                    break;
                }
            }
        }

        // HttpClientInterface $httpClient,
        foreach ($taggedServicesRequestExecutor as $class => $tags) {
            // Pass to HttpTransport RequestExecutor
            $definitionHttpTransport = $container->findDefinition(HttpTransportFactory::class);
            $definitionHttpTransport->addMethodCall('setRequestExecutor', [new Reference($class)]);
        }
    }
}
