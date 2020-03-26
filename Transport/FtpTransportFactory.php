<?php

namespace Vdm\Bundle\LibraryBundle\Transport;

use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;
use Symfony\Component\Messenger\Transport\TransportFactoryInterface;
use Symfony\Component\Messenger\Transport\TransportInterface;
use Vdm\Bundle\LibraryBundle\FileExecutor\FtpFileExecutorInterface;
use Vdm\Bundle\LibraryBundle\FtpClient\FtpClientInterface;

class FtpTransportFactory implements TransportFactoryInterface
{
    private const DSN_PROTOCOL_FTP = 'ftp://';
    private const DSN_PROTOCOL_SFTP = 'sftp://';

    private const DSN_PROTOCOLS = [
        self::DSN_PROTOCOL_FTP,
        self::DSN_PROTOCOL_SFTP
    ];

    /**
     * @var LoggerInterface $logger
     */
    private $logger;

    /**
     * @var FtpClientInterface $ftpClient
     */
    private $ftpClient;

    /**
     * @var FtpFileExecutorInterface $fileExecutor
     */
    private $fileExecutor;

    public function __construct(
        LoggerInterface $logger, 
        FtpClientInterface $ftpClient,
        FtpFileExecutorInterface $fileExecutor
    )
    {
        $this->logger = $logger;
        $this->ftpClient = $ftpClient;
        $this->fileExecutor = $fileExecutor;
    }

    public function createTransport(string $dsn, array $options, SerializerInterface $serializer): TransportInterface
    {
        $mode = $options['mode'];
        $ftp_options = $options['ftp_options'];

        if (!isset($ftp_options['dirpath'])) {
            throw new \InvalidArgumentException('ftp_options.dirpath is not defined');
        }

        if ($mode === 'move' && !isset($ftp_options['storage'])) {
            throw new \InvalidArgumentException('With mode "move", storage ftp_options has to defined');
        }

        return new FtpTransport($this->logger, $this->ftpClient, $this->fileExecutor, $dsn, $mode, $ftp_options);
    }

    public function supports(string $dsn, array $options): bool
    {
        foreach (self::DSN_PROTOCOLS as $protocol) {
            if (0 === strpos($dsn, $protocol)) {
                return true;
            }
        }
        return false;
    }
}
