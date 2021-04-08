<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\MessageHandler;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
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
     * DefaultHandler constructor.
     *
     * @param LoggerInterface|null $vdmLogger
     */
    public function __construct(LoggerInterface $vdmLogger = null)
    {
        $this->logger = $vdmLogger ?? new NullLogger();
    }

    /**
     * Default handler implementation.
     * Does nothing on message because it should be overridden in project code.
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
        // Low priority to be sure it is loaded after project handler if method not overridden
        yield Message::class => [
            'priority' => -1000
        ];
    }
}
