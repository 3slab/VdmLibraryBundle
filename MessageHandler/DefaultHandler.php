<?php

namespace Vdm\Bundle\LibraryBundle\MessageHandler;

use Vdm\Bundle\LibraryBundle\Model\Message;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

/**
 * Class DefaultHandler
 *
 * @package Vdm\Bundle\LibraryBundle\MessageHandler
 */
class DefaultHandler implements MessageSubscriberInterface
{
    /**
     * Default handler implementation.
     * Does nothing on message because it is override by project code.
     *
     * @param Message $message
     */
    public function __invoke(Message $message)
    {
    }

    /**
     * {@inheritDoc}
     */
    public static function getHandledMessages(): iterable
    {
        // Low priority to be sure it is loaded after project handler and so removed from DI
        yield Message::class => [
            'priority' => -1000
        ];
    }
}
