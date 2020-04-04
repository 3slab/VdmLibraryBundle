<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

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
