<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\EventSubscriber\Trace;

use Symfony\Component\Messenger\Envelope;
use Vdm\Bundle\LibraryBundle\Event\CollectWorkerMessageReceivedEvent;
use Vdm\Bundle\LibraryBundle\Model\Trace;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\Event\WorkerMessageReceivedEvent;
use Vdm\Bundle\LibraryBundle\Model\TraceableMessageInterface;

class TraceAddEnterSubscriber implements EventSubscriberInterface
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
     * TraceAddEnterSubscriber constructor.
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
    public function onWorkerMessageReceivedEvent(WorkerMessageReceivedEvent $event)
    {
        $this->addEnterTrace($event->getEnvelope());
    }

    /**
     * Method executed on WorkerMessageReceivedEvent event
     *
     * @param CollectWorkerMessageReceivedEvent $event
     */
    public function onCollectWorkerMessageReceivedEvent(CollectWorkerMessageReceivedEvent $event)
    {
        $this->addEnterTrace($event->getEnvelope());
    }

    /**
     * @param Envelope $envelope
     */
    protected function addEnterTrace(Envelope $envelope)
    {
        $message = $envelope->getMessage();
        if (!$message instanceof TraceableMessageInterface) {
            $this->logger->debug(
                'Trace {traceType} not added as message does not implement TraceableMessageInterface',
                ['traceType' => Trace::ENTER]
            );
            return;
        }

        $message->addTrace(new Trace($this->appName, Trace::ENTER));

        $this->logger->debug('Trace {traceType} added to message with name {traceName}', [
            'traceName' => $this->appName,
            'traceType' => Trace::ENTER
        ]);
    }

    /**
     * {@inheritDoc}
     * @codeCoverageIgnore
     */
    public static function getSubscribedEvents(): array
    {
        return [
            CollectWorkerMessageReceivedEvent::class => 'onCollectWorkerMessageReceivedEvent',
            WorkerMessageReceivedEvent::class => 'onWorkerMessageReceivedEvent',
        ];
    }
}
