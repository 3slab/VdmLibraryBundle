<?php

namespace Vdm\Bundle\LibraryBundle\Client\Ftp;

use Psr\Log\LoggerInterface;

class FtpClientFactory implements FtpClientFactoryInterface
{
    private const DSN_PROTOCOL_FTP = 'ftp';
    private const DSN_PROTOCOL_SFTP = 'sftp';

    /**
     * @var LoggerInterface $messengerLogger
     */
    private $logger;

    public function __construct(LoggerInterface $messengerLogger) {
        $this->logger = $messengerLogger;
    }

    public function create(string $dsn, array $options): FtpClient
    {
        $dsn_regex = '/^((?P<driver>\w+):\/\/)?((?P<user>\w+)?(:(?P<password>\w+))?@)?(?P<host>[\w-\.]+)(:(?P<port>\d+))?$/Uim';
        
        if (false == preg_match($dsn_regex, $dsn, $result)) {
            throw new \InvalidArgumentException("DSN invalide"); 
        }

        if (0 === strpos($result['driver'], self::DSN_PROTOCOL_FTP)) {
            return new FtpClient($result['host'], $result['port'], $result['user'], $result['password'], false, $options, $this->logger);
        } elseif (0 === strpos($result['driver'], self::DSN_PROTOCOL_SFTP)) {
            return new FtpClient($result['host'], $result['port'], $result['user'], $result['password'], true, $options, $this->logger);
        }        
    }
}