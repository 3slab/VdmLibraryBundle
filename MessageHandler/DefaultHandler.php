<?php

namespace App\MessageHandler;

use App\Model\Message;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

/**
 * Class DefaultHandler
 *
 * @package App\MessageHandler
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
