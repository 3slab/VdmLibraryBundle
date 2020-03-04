<?php

namespace Vdm\Bundle\LibraryBundle\DependencyInjection\Compiler;

use Vdm\Bundle\LibraryBundle\Model\Message;
use Vdm\Bundle\LibraryBundle\MessageHandler\DefaultHandler;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class UnloadDefaultHandlerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('messenger.bus.default.messenger.handlers_locator')) {
            return;
        }

        $handlerLocatorDefinition = $container->findDefinition('messenger.bus.default.messenger.handlers_locator');

        /** @var \Symfony\Component\DependencyInjection\Argument\IteratorArgument $handlerDefinitionForMessage */
        $handlerDefinitionForMessage = $handlerLocatorDefinition->getArgument(0)[Message::class];

        $values = $handlerDefinitionForMessage->getValues();
        if (count($values) === 1) {
            // Only one value, so only default handler is loaded
            return;
        }

        /**
         * @var int $key
         * @var \Symfony\Component\DependencyInjection\Reference $ref
         */
        foreach ($values as $key => $ref) {
            $handlerDescriptor = $container->getDefinition((string) $ref);
            if ($handlerDescriptor->getArgument(0) == DefaultHandler::class) {
                // Remove default handler to only execute project one
                unset($values[$key]);
                break;
            }
        }

        $handlerDefinitionForMessage->setValues($values);
    }
}