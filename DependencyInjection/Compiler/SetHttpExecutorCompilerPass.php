<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Vdm\Bundle\LibraryBundle\Executor\Http\AbstractHttpExecutor;
use Vdm\Bundle\LibraryBundle\Executor\Http\DefaultHttpExecutor;
use Vdm\Bundle\LibraryBundle\Transport\Http\HttpTransportFactory;

class SetHttpExecutorCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->has(HttpTransportFactory::class)) {
            return;
        }

        $taggedServicesHttpExecutor = $container->findTaggedServiceIds('vdm_library.http_executor');

        // Unload default http executor if multiple httpExecutor
        if (count($taggedServicesHttpExecutor) > 1) {
            foreach ($taggedServicesHttpExecutor as $id => $tags) {
                if ($id === DefaultHttpExecutor::class) {
                    unset($taggedServicesHttpExecutor[$id]);
                    break;
                }
            }
        }

        $container->setAlias(AbstractHttpExecutor::class, array_key_first($taggedServicesHttpExecutor));
    }
}
