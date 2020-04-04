<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Client\Ftp\Behavior;

use Psr\Log\LoggerInterface;
use Vdm\Bundle\LibraryBundle\Client\Ftp\FtpClientInterface;
use Vdm\Bundle\LibraryBundle\Client\Ftp\Behavior\FtpClientBehaviorFactoryInterface;

class FtpClientBehaviorFactoryRegistry
{
    /** 
     * @var LoggerInterface $logger
    */
    private $logger;

    /** 
     * @var FtpClientInterface $ftpClient
    */
    private $ftpClient;

    /** 
     * @var FtpClientBehaviorFactoryInterface[] $ftpClientBehavior
    */
    private $ftpClientBehavior;

    public function __construct(LoggerInterface $messengerLogger)
    {
        $this->logger = $messengerLogger;
        $this->ftpClientBehavior = [];
    }

    public function addFactory(FtpClientBehaviorFactoryInterface $ftpClientBehavior, string $priority)
    {
        $this->ftpClientBehavior[$priority] = $ftpClientBehavior;
        ksort($this->ftpClientBehavior);
    }

    public function create($ftpClient, array $options)
    {
        $this->ftpClient = $ftpClient;

        foreach ($this->ftpClientBehavior as $ftpClientBehavior) {
            if ($ftpClientBehavior->support($options)) {
                $this->ftpClient = $ftpClientBehavior->createDecoratedFtpClient($this->logger, $this->ftpClient, $options);
            }
        }

        return $this->ftpClient;
    }
}
