<?php

namespace Vdm\Bundle\LibraryBundle\Client\Ftp;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Vdm\Bundle\LibraryBundle\Client\Ftp\FtpClientInterface;
use Vdm\Bundle\LibraryBundle\Client\Ftp\Event\FtpClientErrorEvent;
use Vdm\Bundle\LibraryBundle\Client\Ftp\Event\FtpClientReceivedEvent;

class MonitoringFtpClientBehavior extends DecoratorFtpClient
{
    /**
     * @var LoggerInterface $logger
     */
    protected $logger;

    /**
     * @var FtpClientInterface $ftpClient
     */
    protected $ftpClient;

    /**
     * @var EventDispatcherInterface $eventDispatcher
     */
    private $eventDispatcher;

    /**
     * MonitoringFtpClientBehavior constructor
     */
    public function __construct(
        LoggerInterface $logger, 
        FtpClientInterface $ftpClient, 
        EventDispatcherInterface $eventDispatcher
    )
    {
        parent::__construct($logger, $ftpClient);
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * {@inheritDoc}
     */
    public function get(string $filename): ?string
    {        
        try {
            $file = $this->ftpClientDecorated->get($filename);

            $this->eventDispatcher->dispatch(new FtpClientReceivedEvent($file));
        } catch(\Exception $exception) {
            $this->eventDispatcher->dispatch(new FtpClientErrorEvent());
            $this->logger->error(sprintf('%s: %s', get_class($exception), $exception->getMessage()));

            throw $exception;
        }

        return $file;
    }

    /**
     * {@inheritDoc}
     */
    public function list(string $dirpath): ?array
    {
        return $this->ftpClientDecorated->list($dirpath);
    }
}
