<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\DependencyInjection\Compiler;

use Vdm\Bundle\LibraryBundle\EventListener\ErrorStopWorkerListener;
use Vdm\Bundle\LibraryBundle\EventListener\ErrorRethrowAfterWorkerStoppedListener;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class UnloadStopWorkerOnErrorPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        // Failed transport is enabled
        if ($container->hasDefinition('messenger.failure.send_failed_message_to_failure_transport_listener')) {
            $container->removeDefinition(ErrorStopWorkerListener::class);
            $container->removeDefinition(ErrorRethrowAfterWorkerStoppedListener::class);
            return;
        }
    }
}
