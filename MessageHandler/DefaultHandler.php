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
abstract class DefaultHandler implements MessageSubscriberInterface
{
    /** 
     * @var LoggerInterface $logger
    */
    protected $logger;

    /** 
     * @var MessageBusInterface $bus
    */
    protected $bus;

    public function __construct(LoggerInterface $vdmLogger, MessageBusInterface $bus)
    {
        $this->logger = $vdmLogger;
        $this->bus = $bus;
    }

    /**
     * Default handler implementation.
     * Does nothing on message because it should be overriden in project code.
     * 
     * @codeCoverageIgnore
     *
     * @param Message $message
     */
    public function __invoke(Message $message)
    {
        $this->logger->warning(
            "No change to Vdm\Bundle\LibraryBundle\MessageHandler\DefaultHandler implementation"
        );
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
