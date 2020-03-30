<?php

namespace Vdm\Bundle\LibraryBundle\FtpClient\Behavior;

use Psr\Log\LoggerInterface;
use Vdm\Bundle\LibraryBundle\FtpClient\FtpClientInterface;

interface FtpClientBehaviorFactoryInterface
{
    public static function priority(int $priority = 0);

    public function createDecoratedFtpClient(LoggerInterface $logger, FtpClientInterface $ftpClient, array $options);

    public function support(array $options);
}
