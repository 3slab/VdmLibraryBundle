<?php

namespace Vdm\Bundle\LibraryBundle\Transport\Ftp;

use League\Flysystem\FileExistsException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Transport\TransportInterface;
use Vdm\Bundle\LibraryBundle\Executor\Ftp\AbstractFtpExecutor;
use Vdm\Bundle\LibraryBundle\Model\Message;

class FtpTransport implements TransportInterface
{
    /** 
     * @var LoggerInterface $logger
    */
    private $logger;

    /** 
     * @var AbstractFtpExecutor $ftpExecutor
    */
    private $ftpExecutor;

    /** 
     * @var string $dsn
    */
    private $dsn;

    /** 
     * @var string $mode
    */
    private $mode;

    /** 
     * @var array $options
    */
    private $options;

    public function __construct(
        LoggerInterface $logger,
        AbstractFtpExecutor $ftpExecutor, 
        string $dsn, 
        string $mode, 
        array $options
    )
    {
        $this->logger = $logger;
        $this->ftpExecutor = $ftpExecutor;
        $this->dsn = $dsn;
        $this->mode = $mode;
        $this->options = $options;
    }

    public function get(): iterable
    {
        $this->logger->debug('get called');

        $files = $this->ftpExecutor->getFtpClient()->list($this->options['dirpath']);

        return $this->ftpExecutor->execute($files);
    }

    public function ack(Envelope $envelope): void
    {
        $this->logger->debug('ack called');
        $filesystem = $this->ftpExecutor->getFtpClient()->getFilesystem();
        $data = $envelope->getMessage()->getPayload();
        
        switch ($this->mode) {
            case 'move':    
                try {
                    $filesystem->copy($data['path'], $this->options['storage'].'/'.$data['basename']);
                    $filesystem->delete($data['path']);
                    $this->logger->info(sprintf('Move file %s to folder %s', $data['basename'], $this->options['storage']));
                } catch (FileExistsException $exception) {
                    // Que faire si le fichier existe déjà ?
                    throw $exception;
                }
            break;
            case 'delete':
                $filesystem->delete($data['path']);
                $this->logger->info(sprintf('Delete file %s', $data['basename']));
            break;
            default:
            break;
        }
    }

    public function reject(Envelope $envelope): void
    {        
        $this->logger->debug('reject called');
    }

    public function send(Envelope $envelope): Envelope
    {
        $this->logger->debug('send called');
        
        return $envelope;
    }
}
