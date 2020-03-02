<?php

namespace App\EventListener;

use App\Model\Trace;
use App\Model\Message;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\Event\WorkerMessageReceivedEvent;

class TraceAddEnterListener implements EventSubscriberInterface
{
    /**
     * @var string
     */
    private $appName;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * TraceAddEnterListener constructor.
     *
     * @param string $appName
     * @param LoggerInterface|null $messengerLogger
     */
    public function __construct(string $appName, LoggerInterface $messengerLogger = null)
    {
        $this->appName = $appName;
        $this->logger = $messengerLogger;
    }

    /**
     * Method executed on WorkerMessageReceivedEvent event
     *
     * @param WorkerMessageReceivedEvent $event
     */
    public function onWorkerMessageReceived(WorkerMessageReceivedEvent $event)
    {
        $message = $event->getEnvelope()->getMessage();
        if (!$message instanceof Message) {
            return;
        }

        $message->addTrace(new Trace($this->appName, Trace::ENTER));

        if (null !== $this->logger) {
            $this->logger->info('WorkerMessageReceivedEvent - {appName} {traceType} trace added to message', [
                'appName' => $this->appName,
                'traceType' => Trace::ENTER
            ]);
        }
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            WorkerMessageReceivedEvent::class => 'onWorkerMessageReceived',
        ];
    }
}
