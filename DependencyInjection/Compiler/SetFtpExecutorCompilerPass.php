<?php

namespace Vdm\Bundle\LibraryBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Vdm\Bundle\LibraryBundle\Executor\Ftp\AbstractFtpExecutor;
use Vdm\Bundle\LibraryBundle\Executor\Ftp\DefaultFtpExecutor;
use Vdm\Bundle\LibraryBundle\Transport\Ftp\FtpTransportFactory;

class SetFtpExecutorCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->has(FtpTransportFactory::class)) {
            return;
        }

        $taggedServicesFtpExecutor = $container->findTaggedServiceIds('vdm_library.ftp_executor');

        // Unload default ftp executor if multiple ftpExecutor
        if (count($taggedServicesFtpExecutor) > 1) {
            foreach ($taggedServicesFtpExecutor as $id => $tags) {
                if ($id === DefaultFtpExecutor::class) {
                    unset($taggedServicesFtpExecutor[$id]);
                    break;
                }
            }
        }

        $container->setAlias(AbstractFtpExecutor::class, array_key_first($taggedServicesFtpExecutor));
    }
}
