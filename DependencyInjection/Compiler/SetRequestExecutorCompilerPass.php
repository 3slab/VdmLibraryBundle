<?php

namespace Vdm\Bundle\LibraryBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Vdm\Bundle\LibraryBundle\Executor\Http\AbstractHttpExecutor;
use Vdm\Bundle\LibraryBundle\Executor\Http\DefaultHttpExecutor;
use Vdm\Bundle\LibraryBundle\Transport\Http\HttpTransportFactory;

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
                if ($id === DefaultHttpExecutor::class) {
                    unset($taggedServicesRequestExecutor[$id]);
                    break;
                }
            }
        }

        $container->setAlias(AbstractHttpExecutor::class, array_key_first($taggedServicesRequestExecutor));
    }
}
