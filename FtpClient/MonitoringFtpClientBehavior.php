<?php

namespace Vdm\Bundle\LibraryBundle\FtpClient;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Vdm\Bundle\LibraryBundle\FtpClient\FtpClientInterface;
use Vdm\Bundle\LibraryBundle\FtpClient\Event\FtpClientReceivedEvent;

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
    public function get(string $dsn, array $options): ?array
    {        
        try {
            $file = $this->ftpClientDecorated->get($dsn, $options);

            $this->eventDispatcher->dispatch(new FtpClientReceivedEvent($file));
        } catch(\InvalidArgumentException $exception) {
            $this->eventDispatcher->dispatch(new FtpClientReceivedEvent(null));
            $this->logger->error(sprintf('%s: %s', get_class($exception), $exception->getMessage()));

            throw $exception;
        } catch(\Exception $exception) {
            $this->eventDispatcher->dispatch(new FtpClientReceivedEvent(null));
            $this->logger->error(sprintf('%s: %s', get_class($exception), $exception->getMessage()));

            throw $exception;
        }

        return $file;
    }
}
