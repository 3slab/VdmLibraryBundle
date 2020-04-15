<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

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
     * @codeCoverageIgnore
     *
     * @param Message $message
     */
    public function __invoke(Message $message)
    {
        $this->messengerLogger->debug("Execution of default handler");

        // If the Handler dispatches the message further, you will need to add the HandledStamp to the message during dispatch.
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
