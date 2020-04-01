<?php

namespace Vdm\Bundle\LibraryBundle\Transport;

use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;
use Symfony\Component\Messenger\Transport\TransportFactoryInterface;
use Symfony\Component\Messenger\Transport\TransportInterface;
use Vdm\Bundle\LibraryBundle\FileExecutor\FtpFileExecutorInterface;
use Vdm\Bundle\LibraryBundle\FtpClient\Behavior\FtpClientBehaviorFactoryRegistry;
use Vdm\Bundle\LibraryBundle\FtpClient\FtpClientFactoryInterface;
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
     * @var FtpClientFactoryInterface $ftpClientFactory
     */
    private $ftpClientFactory;

    /**
     * @var FtpClientInterface $ftpClient
     */
    private $ftpClient;

    /**
     * @var FtpFileExecutorInterface $fileExecutor
     */
    private $fileExecutor;

    /**
     * @var FtpClientBehaviorFactoryRegistry $ftpClientBehaviorFactoryRegistry
     */
    private $ftpClientBehaviorFactoryRegistry;

    public function __construct(
        LoggerInterface $logger, 
        FtpClientFactoryInterface $ftpClientFactory,
        FtpFileExecutorInterface $fileExecutor,
        FtpClientBehaviorFactoryRegistry $ftpClientBehaviorFactoryRegistry
    )
    {
        $this->logger = $logger;
        $this->ftpClientFactory = $ftpClientFactory;
        $this->fileExecutor = $fileExecutor;
        $this->ftpClientBehaviorFactoryRegistry = $ftpClientBehaviorFactoryRegistry;
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

        $this->ftpClient = $this->ftpClientFactory->create($dsn, $options);
        $this->ftpClient = $this->ftpClientBehaviorFactoryRegistry->create($this->ftpClient, $options);

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
