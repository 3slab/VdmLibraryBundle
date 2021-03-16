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
     * @param LoggerInterface|null $vdmLogger
     */
    public function __construct(string $appName, LoggerInterface $vdmLogger = null)
    {
        $this->appName = $appName;
        $this->logger = $vdmLogger ?? new NullLogger();
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

        $payload = $message->getPayload();
        if (empty($payload)) {
            return;
        }

        $message->addTrace(new Trace($this->appName, Trace::ENTER));

        $this->logger->info('WorkerMessageReceivedEvent - {appName} {traceType} trace added to message', [
            'appName' => $this->appName,
            'traceType' => Trace::ENTER
        ]);
    }

    /**
     * {@inheritDoc}
     * @codeCoverageIgnore
     */
    public static function getSubscribedEvents()
    {
        return [
            WorkerMessageReceivedEvent::class => 'onWorkerMessageReceived',
        ];
    }
}
