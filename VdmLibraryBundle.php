<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Vdm\Bundle\LibraryBundle\DependencyInjection\Compiler\ElasticClientBehaviorCreateCompilerPass;
use Vdm\Bundle\LibraryBundle\DependencyInjection\Compiler\FtpClientBehaviorCreateCompilerPass;
use Vdm\Bundle\LibraryBundle\DependencyInjection\Compiler\SetDoctrineExecutorCompilerPass;
use Vdm\Bundle\LibraryBundle\DependencyInjection\Compiler\SetFtpExecutorCompilerPass;
use Vdm\Bundle\LibraryBundle\DependencyInjection\Compiler\SetStatsStorageCompilerPass;
use Vdm\Bundle\LibraryBundle\DependencyInjection\Compiler\UnloadDefaultHandlerPass;
use Vdm\Bundle\LibraryBundle\DependencyInjection\Compiler\UnloadStopWorkerOnErrorPass;

class VdmLibraryBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new UnloadDefaultHandlerPass());
        $container->addCompilerPass(new UnloadStopWorkerOnErrorPass());
        $container->addCompilerPass(new SetStatsStorageCompilerPass());
        $container->addCompilerPass(new SetFtpExecutorCompilerPass());
        $container->addCompilerPass(new FtpClientBehaviorCreateCompilerPass());
        $container->addCompilerPass(new ElasticClientBehaviorCreateCompilerPass());
        $container->addCompilerPass(new SetDoctrineExecutorCompilerPass());
    }
}
