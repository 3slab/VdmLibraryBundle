<?php

namespace Vdm\Bundle\LibraryBundle\Client\Ftp;

use League\Flysystem\Filesystem;
use Psr\Log\LoggerInterface;
use Vdm\Bundle\LibraryBundle\Client\Ftp\FtpClientInterface;

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
    public function get(array $file): array
    {
        return $this->ftpClientDecorated->get($file);
    }

    /**
     * {@inheritDoc}
     */
    public function list(string $dirpath): ?array
    {
        return $this->ftpClientDecorated->list($dirpath);
    }

    /**
     * {@inheritDoc}
     */
    public function getFileSystem(): Filesystem
    {
        return $this->ftpClientDecorated->getFileSystem();
    }
}
