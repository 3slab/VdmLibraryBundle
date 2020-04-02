<?php

namespace Vdm\Bundle\LibraryBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Vdm\Bundle\LibraryBundle\Executor\Ftp\FtpExecutorInterface;
use Vdm\Bundle\LibraryBundle\Executor\Ftp\DefaultFtpExecutor;
use Vdm\Bundle\LibraryBundle\Transport\Ftp\FtpTransportFactory;

class SetFtpFileExecutorCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->has(FtpTransportFactory::class)) {
            return;
        }

        $taggedServicesFileExecutor = $container->findTaggedServiceIds('vdm_library.ftp_file_executor');

        // Unload default file executor if multiple fileExecutor
        if (count($taggedServicesFileExecutor) > 1) {
            foreach ($taggedServicesFileExecutor as $id => $tags) {
                if ($id === DefaultFtpExecutor::class) {
                    unset($taggedServicesFileExecutor[$id]);
                    break;
                }
            }
        }

        $container->setAlias(FtpExecutorInterface::class, array_key_first($taggedServicesFileExecutor));
    }
}
