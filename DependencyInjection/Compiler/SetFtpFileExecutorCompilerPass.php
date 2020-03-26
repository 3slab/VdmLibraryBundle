<?php

namespace Vdm\Bundle\LibraryBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Vdm\Bundle\LibraryBundle\FileExecutor\FtpFileExecutorInterface;
use Vdm\Bundle\LibraryBundle\FileExecutor\DefaultFileExecutor;
use Vdm\Bundle\LibraryBundle\Transport\FtpTransportFactory;

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
                if ($id === DefaultFileExecutor::class) {
                    unset($taggedServicesFileExecutor[$id]);
                    break;
                }
            }
        }

        $container->setAlias(FtpFileExecutorInterface::class, array_key_first($taggedServicesFileExecutor));
    }
}
