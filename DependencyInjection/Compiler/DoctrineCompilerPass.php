<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Vdm\Bundle\LibraryBundle\EventListener\Doctrine\NoDataLossListener;
use Vdm\Bundle\LibraryBundle\Monitoring\StatsStorageInterface;
use Vdm\Bundle\LibraryBundle\Transport\Doctrine\DoctrineSender;
use Vdm\Bundle\LibraryBundle\Transport\Doctrine\DoctrineSenderFactory;
use Vdm\Bundle\LibraryBundle\Transport\Doctrine\DoctrineTransport;
use Vdm\Bundle\LibraryBundle\Transport\Doctrine\DoctrineTransportFactory;

class DoctrineCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        // These two don't need to be registered as services.
        if ($container->has(DoctrineTransport::class)) {
            $container->removeDefinition(DoctrineTransport::class);
        }

        if ($container->has(DoctrineSender::class)) {
            $container->removeDefinition(DoctrineSender::class);
        }

        $doctrine = $container->getParameter('vdm_library.doctrine');

        $listenerDefinition = null;

        if ($container->has(NoDataLossListener::class)) {
            $listenerDefinition = $container->findDefinition(NoDataLossListener::class);
            $listenerDefinition->addTag('doctrine.event_subscriber', [ 'connection' => 'default']);
            $listenerDefinition->setArgument(1, $doctrine['nullable_fields_whitelist']);
        }

        // If user has defined a specific connection, overwrite default connection with this one.
        if (!empty($doctrine['connection'])) {
            $connectionService = sprintf('doctrine.orm.%s_entity_manager', $doctrine['connection']);

            if (!$container->has($connectionService)) {
                throw new \RuntimeException(sprintf('Connection %s does not exist', $connectionService));
            }

            $connectionDefinition = $container->findDefinition($connectionService);

            if ($listenerDefinition) {
                $listenerDefinition->setArgument(0, $connectionDefinition);
            }

            if ($container->has(DoctrineSenderFactory::class)) {
                $senderFactoryDefinition = $container->findDefinition(DoctrineSenderFactory::class);
                $senderFactoryDefinition->setArgument(0, $connectionDefinition);
            }
        }
    }
}
