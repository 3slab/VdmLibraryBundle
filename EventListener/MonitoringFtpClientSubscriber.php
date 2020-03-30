<?php

namespace Vdm\Bundle\LibraryBundle\EventListener;

use Vdm\Bundle\LibraryBundle\Monitoring\StatsStorageInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Psr\Log\LoggerInterface;
use Vdm\Bundle\LibraryBundle\FtpClient\Event\FtpClientReceivedEvent;
use Vdm\Bundle\LibraryBundle\Monitoring\Model\FtpClientResponseStat;

class MonitoringFtpClientSubscriber implements EventSubscriberInterface
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
     * MonitoringFtpClientSubscriber constructor.
     *
     * @param StatsStorageInterface $storage
     * @param LoggerInterface|null $messengerLogger
     */
    public function __construct(StatsStorageInterface $storage, LoggerInterface $messengerLogger = null)
    {
        $this->storage = $storage;
        $this->logger = $messengerLogger;
    }

    /**
     * Method executed on FtpClientReceivedEvent event
     *
     * @param FtpClientReceivedEvent $event
     */
    public function onFtpClientReceivedResponseEvent(FtpClientReceivedEvent $event)
    {
        $file = $event->getFile();
        $size = null;
        $error = null;

        if ($file !== null) {
            $size = $file['size'];
        } else {
            $error = true;
        }
        
        $this->logger->debug(sprintf('size: %s', $size));
        $this->logger->debug(sprintf('error: %d', $error));

        $ftpClientResponseStat = new FtpClientResponseStat($error, $size);
        $this->storage->sendFtpResponseStat($ftpClientResponseStat);
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            FtpClientReceivedEvent::class => 'onFtpClientReceivedResponseEvent',
        ];
    }
}
