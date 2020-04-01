<?php

namespace Vdm\Bundle\LibraryBundle\FtpClient;

use League\Flysystem\Filesystem;
use Psr\Log\LoggerInterface;
use Vdm\Bundle\LibraryBundle\FtpClient\FtpClientInterface;

abstract class DecoratorFtpClient implements FtpClientInterface
{
    /** 
     * @var LoggerInterface $logger
    */
    protected $logger;

    /** 
     * @var FtpClientInterface $ftpClient
    */
    protected $ftpClientDecorated;

    public function __construct(LoggerInterface $logger, FtpClientInterface $ftpClient) {
        $this->ftpClientDecorated = $ftpClient;
        $this->logger = $logger;
    }
    
    /**
     * {@inheritDoc}
     */
    public function get(string $dirpath): ?array
    {
        return $this->ftpClientDecorated->get($dirpath);
    }

    /**
     * {@inheritDoc}
     */
    public function getFileSystem(): Filesystem
    {
        return $this->ftpClientDecorated->getFileSystem();
    }
}
