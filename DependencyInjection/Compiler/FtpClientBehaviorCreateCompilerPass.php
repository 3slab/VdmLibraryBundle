<?php

namespace Vdm\Bundle\LibraryBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Vdm\Bundle\LibraryBundle\FtpClient\Behavior\FtpClientBehaviorFactoryRegistry;
use Vdm\Bundle\LibraryBundle\FtpClient\FtpClient;
use Vdm\Bundle\LibraryBundle\FtpClient\FtpClientInterface;

class FtpClientBehaviorCreateCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->has(FtpClientBehaviorFactoryRegistry::class)) {
            return;
        }

        $definition = $container->findDefinition(FtpClientBehaviorFactoryRegistry::class);
        $taggedServices = $container->findTaggedServiceIds('vdm_library.ftp_decorator_factory');

        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall('addFactory', [new Reference($id), $id::priority()]);
        } 

        $container->setAlias(FtpClientInterface::class, FtpClient::class);        
    }
}
