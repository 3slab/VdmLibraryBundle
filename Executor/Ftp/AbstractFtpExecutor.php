<?php

namespace Vdm\Bundle\LibraryBundle\Executor\Ftp;

use Vdm\Bundle\LibraryBundle\Client\Ftp\FtpClientInterface;

abstract class AbstractFtpExecutor
{
    /** 
     * @var FtpClientInterface $ftpClient
    */
    protected $ftpClient;

    public function __construct() {
    }

    abstract public function execute(array $files): iterable;

    public function getFtpClient(): FtpClientInterface
    {
        return $this->ftpClient;
    }

    public function setFtpClient(FtpClientInterface $ftpClient)
    {
        $this->ftpClient = $ftpClient;
    }
}
