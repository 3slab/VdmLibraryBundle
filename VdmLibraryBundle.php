<?php

namespace Vdm\Bundle\LibraryBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Vdm\Bundle\LibraryBundle\DependencyInjection\Compiler\UnloadDefaultHandlerPass;
use Vdm\Bundle\LibraryBundle\DependencyInjection\Compiler\UnloadStopWorkerOnErrorPass;
use Vdm\Bundle\LibraryBundle\DependencyInjection\Compiler\SetStatsStorageCompilerPass;

class VdmLibraryBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new UnloadDefaultHandlerPass());
        $container->addCompilerPass(new UnloadStopWorkerOnErrorPass());
        $container->addCompilerPass(new SetStatsStorageCompilerPass());
    }
}
