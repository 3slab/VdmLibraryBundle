<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\EventListener;

use Vdm\Bundle\LibraryBundle\Model\Trace;
use Vdm\Bundle\LibraryBundle\Model\Message;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\Event\SendMessageToTransportsEvent;

class TraceAddExitListener implements EventSubscriberInterface
{
    /**
     * @var string
     */
    private $appName;

    /**
     * @var LoggerInterface|null
     */
    private $logger;

    /**
     * TraceAddExitListener constructor.
     *
     * @param string $appName
     * @param LoggerInterface|null $messengerLogger
     */
    public function __construct(string $appName, LoggerInterface $messengerLogger = null)
    {
        $this->appName = $appName;
        $this->logger = $messengerLogger ?? new NullLogger();
    }

    /**
     * Method executed on SendMessageToTransportsEvent event
     *
     * @param SendMessageToTransportsEvent $event
     */
    public function onSendMessageToTransport(SendMessageToTransportsEvent $event)
    {
        $message = $event->getEnvelope()->getMessage();
        if (!$message instanceof Message) {
            return;
        }

        $message->addTrace(new Trace($this->appName, Trace::EXIT));

        $this->logger->info('SendMessageToTransportsEvent - {appName} {traceType} trace added to message', [
            'appName' => $this->appName,
            'traceType' => Trace::EXIT
        ]);
    }

    /**
     * {@inheritDoc}
     * @codeCoverageIgnore
     */
    public static function getSubscribedEvents()
    {
        return [
            SendMessageToTransportsEvent::class => 'onSendMessageToTransport',
        ];
    }
}
