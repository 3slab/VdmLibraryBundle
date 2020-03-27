<?php

namespace Vdm\Bundle\LibraryBundle\FtpClient\Behavior;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Vdm\Bundle\LibraryBundle\FtpClient\FtpClientInterface;
use Vdm\Bundle\LibraryBundle\FtpClient\MonitoringFtpClientBehavior;

class MonitoringFtpClientBehaviorFactory implements FtpClientBehaviorFactoryInterface
{
    private $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public static function priority(int $priority = -100)
    {
        return $priority;
    }

    public function createDecoratedFtpClient(LoggerInterface $logger, FtpClientInterface $ftpClient, array $options)
    {
        return new MonitoringFtpClientBehavior($logger, $ftpClient, $this->eventDispatcher);
    }

    public function support(array $options)
    {
        if (isset($options['monitoring']['enabled']) && $options['monitoring']['enabled'] === true) {
            return true;
        }

        return false;
    }
}
