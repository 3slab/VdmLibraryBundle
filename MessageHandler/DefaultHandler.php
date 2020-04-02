<?php

namespace Vdm\Bundle\LibraryBundle\MessageHandler;

use Psr\Log\LoggerInterface;
use Vdm\Bundle\LibraryBundle\Model\Message;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * Class DefaultHandler
 *
 * @package Vdm\Bundle\LibraryBundle\MessageHandler
 */
class DefaultHandler implements MessageSubscriberInterface
{
    /** 
     * @var LoggerInterface $messengerLogger
    */
    protected $messengerLogger;

    /** 
     * @var MessageBusInterface $bus
    */
    protected $bus;

    public function __construct(LoggerInterface $messengerLogger, MessageBusInterface $bus)
    {
        $this->messengerLogger = $messengerLogger;
        $this->bus = $bus;
    }

    /**
     * Default handler implementation.
     * Does nothing on message because it is override by project code.
     *
     * @param Message $message
     */
    public function __invoke(Message $message)
    {
        $this->messengerLogger->debug("Execution of default handler");

        $this->bus->dispatch($message);
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
