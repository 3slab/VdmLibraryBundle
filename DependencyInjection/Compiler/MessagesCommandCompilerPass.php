<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Reference;
use Vdm\Bundle\LibraryBundle\Command\CollectMessagesCommand;
use Vdm\Bundle\LibraryBundle\Command\ConsumeMessagesCommand;

/**
 * Class MessagesCommandCompilerPass
 * @package Vdm\Bundle\LibraryBundle\DependencyInjection\Compiler
 */
class MessagesCommandCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $consumeCommandDefinition = $container->findDefinition(ConsumeMessagesCommand::class);
        $collectCommandDefinition = $container->findDefinition(CollectMessagesCommand::class);
        if (!$container->getDefinition('console.command.messenger_consume_messages')) {
            $container->removeDefinition($consumeCommandDefinition);
            $container->removeDefinition($collectCommandDefinition);
            return;
        }

        $sfConsumeCommandDefinition = $container->getDefinition('console.command.messenger_consume_messages');
        $hasRoutableMessageBus = $container->hasDefinition('messenger.routable_message_bus');
        if ($hasRoutableMessageBus) {
            $consumeCommandDefinition->setArgument(0, new Reference('messenger.routable_message_bus'));
            $collectCommandDefinition->setArgument(0, new Reference('messenger.routable_message_bus'));
        }

        $consumeCommandDefinition->setArgument(1, new Reference('messenger.receiver_locator'));
        $consumeCommandDefinition->setArgument(2, new Reference('event_dispatcher'));
        $consumeCommandDefinition->setArgument(3, new Reference('logger', ContainerInterface::NULL_ON_INVALID_REFERENCE));

        $collectCommandDefinition->setArgument(1, new Reference('messenger.receiver_locator'));
        $collectCommandDefinition->setArgument(2, new Reference('event_dispatcher'));
        $collectCommandDefinition->setArgument(3, new Reference('logger', ContainerInterface::NULL_ON_INVALID_REFERENCE));

        $receiverNames = $sfConsumeCommandDefinition->getArgument(4);
        $consumeCommandDefinition->setArgument(4, array_values($receiverNames));
        $collectCommandDefinition->setArgument(4, array_values($receiverNames));
    }
}