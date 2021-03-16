<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\EventListener;

use Vdm\Bundle\LibraryBundle\Model\Message;
use Vdm\Bundle\LibraryBundle\Monitoring\StatsStorageInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\Event\WorkerMessageFailedEvent;
use Vdm\Bundle\LibraryBundle\Monitoring\Model\ErrorStat;

class MonitoringWorkerMessageFailedListener implements EventSubscriberInterface
{
    /**
     * @var StatsStorageInterface
     */
    private $storage;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * MonitoringWorkerHandledMessageListener constructor.
     *
     * @param StatsStorageInterface $storage
     * @param LoggerInterface|null $vdmLogger
     */
    public function __construct(StatsStorageInterface $storage, LoggerInterface $vdmLogger = null)
    {
        $this->storage = $storage;
        $this->logger = $vdmLogger;
    }

    /**
     * Method executed on WorkerMessageFailedEvent event
     *
     * @param WorkerMessageFailedEvent $event
     */
    public function onWorkerMessageFailed(WorkerMessageFailedEvent $event)
    {
        $message = $event->getEnvelope()->getMessage();
        if (!$message instanceof Message) {
            return;
        }

        $error = 1;

        $errorStat = new ErrorStat($error);
        $this->storage->sendErrorStat($errorStat);
        
        if (null !== $this->logger) {
            $this->logger->info('WorkerMessageReceivedEvent - Consumer stats sent');
        }
    }

    /**
     * {@inheritDoc}
     * @codeCoverageIgnore
     */
    public static function getSubscribedEvents()
    {
        return [
            WorkerMessageFailedEvent::class => 'onWorkerMessageFailed',
        ];
    }
}
