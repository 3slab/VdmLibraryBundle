<?php

namespace Vdm\Bundle\LibraryBundle\EventListener;

use Vdm\Bundle\LibraryBundle\Monitoring\StatsStorageInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Psr\Log\LoggerInterface;
use Vdm\Bundle\LibraryBundle\Client\Ftp\Event\FtpClientErrorEvent;
use Vdm\Bundle\LibraryBundle\Client\Ftp\Event\FtpClientReceivedEvent;
use Vdm\Bundle\LibraryBundle\Monitoring\Model\FtpClientErrorStat;
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
    public function onFtpClientReceivedEvent(FtpClientReceivedEvent $event)
    {
        $content = $event->getContent();
        $size = strlen($content);
        
        $this->logger->debug(sprintf('size: %s', $size));

        $ftpClientResponseStat = new FtpClientResponseStat($size);
        $this->storage->sendFtpResponseStat($ftpClientResponseStat);
    }

    /**
     * Method executed on FtpClientErrorEvent event
     *
     * @param FtpClientErrorEvent $event
     */
    public function onFtpClientErrorEvent(FtpClientErrorEvent $event)
    {
        $error = $event->getError();
        
        $this->logger->debug(sprintf('error: %d', $error));

        $ftpClienErrorStat = new FtpClientErrorStat($error);
        $this->storage->sendFtpErrorStat($ftpClienErrorStat);
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            FtpClientReceivedEvent::class => 'onFtpClientReceivedEvent',
            FtpClientErrorEvent::class => 'onFtpClientErrorEvent',
        ];
    }
}
