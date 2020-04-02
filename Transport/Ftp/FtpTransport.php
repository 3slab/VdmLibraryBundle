<?php

namespace Vdm\Bundle\LibraryBundle\Transport\Ftp;

use League\Flysystem\FileExistsException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Transport\TransportInterface;
use Vdm\Bundle\LibraryBundle\Executor\Ftp\AbstractFtpExecutor;

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
        return $this->ftpExecutor->execute($this->options);
    }

    public function ack(Envelope $envelope): void
    {
        $filesystem = $this->ftpExecutor->getFtpClient()->getFilesystem();
        $message = $envelope->getMessage();
        $metadatas = $message->getMetadatas();  
        
        switch ($this->mode) {
            case 'move':    
                try {
                    $filesystem->copy($metadatas['path'], $this->options['storage'].'/'.$metadatas['basename']);
                    $filesystem->delete($metadatas['path']);
                    $this->logger->info(sprintf('Move file %s to folder %s', $metadatas['basename'], $this->options['storage']));
                } catch (FileExistsException $exception) {
                    // Que faire si le fichier existe déjà ?
                    throw $exception;
                }
            break;
            case 'delete':
                $filesystem->delete($metadatas['path']);
                $this->logger->info(sprintf('Delete file %s', $metadatas['basename']));
            break;
            default:
            break;
        }
    }

    public function reject(Envelope $envelope): void
    {        
    }

    public function send(Envelope $envelope): Envelope
    {
    }
}
