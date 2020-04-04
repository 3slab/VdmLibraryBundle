<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Client\Ftp\Behavior;

use Psr\Log\LoggerInterface;
use Vdm\Bundle\LibraryBundle\Client\Ftp\FtpClientInterface;

interface FtpClientBehaviorFactoryInterface
{
    public static function priority(int $priority = 0);

    public function createDecoratedFtpClient(LoggerInterface $logger, FtpClientInterface $ftpClient, array $options);

    public function support(array $options);
}
