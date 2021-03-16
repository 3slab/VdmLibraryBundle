<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\EventSubscriber\Trace;

use Vdm\Bundle\LibraryBundle\Model\Trace;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\Event\SendMessageToTransportsEvent;
use Vdm\Bundle\LibraryBundle\Model\TraceableMessageInterface;

class TraceAddExitSubscriber implements EventSubscriberInterface
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
     * @param LoggerInterface|null $vdmLogger
     */
    public function __construct(string $appName, LoggerInterface $vdmLogger = null)
    {
        $this->appName = $appName;
        $this->logger = $vdmLogger ?? new NullLogger();
    }

    /**
     * Method executed on SendMessageToTransportsEvent event
     *
     * @param SendMessageToTransportsEvent $event
     */
    public function onSendMessageToTransport(SendMessageToTransportsEvent $event)
    {
        $message = $event->getEnvelope()->getMessage();
        if (!$message instanceof TraceableMessageInterface) {
            $this->logger->debug(
                'Trace {traceType} not added as message does not implement TraceableMessageInterface',
                ['traceType' => Trace::ENTER]
            );
            return;
        }

        // Messenger does not provide the sent transport name on this event because the message
        // could be sent to multiple transports. As a ack, for now, we get the last enter trace
        // and use the same name for the exit trace
        $trace = $message->getLastTrace();
        $traceName = $trace ? $trace->getName() : $this->appName;
        $message->addTrace(new Trace($traceName, Trace::EXIT));

        $this->logger->debug('Trace {traceType} added to message with name {traceName}', [
            'traceName' => $traceName,
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
