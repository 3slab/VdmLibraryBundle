<?php

namespace Vdm\Bundle\LibraryBundle\Transport;

use League\Flysystem\FileExistsException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Transport\TransportInterface;
use Vdm\Bundle\LibraryBundle\FileExecutor\FtpFileExecutorInterface;
use Vdm\Bundle\LibraryBundle\FtpClient\FtpClientInterface;
use Vdm\Bundle\LibraryBundle\Model\Message;
use Vdm\Bundle\LibraryBundle\Stamp\StopAfterHandleStamp;

class FtpTransport implements TransportInterface
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
     * @var FtpFileExecutorInterface $fileExecutor
    */
    private $fileExecutor;

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
        FtpClientInterface $ftpClient, 
        FtpFileExecutorInterface $fileExecutor, 
        string $dsn, 
        string $mode, 
        array $options
    )
    {
        $this->logger = $logger;
        $this->ftpClient = $ftpClient;
        $this->fileExecutor = $fileExecutor;
        $this->dsn = $dsn;
        $this->mode = $mode;
        $this->options = $options;
    }

    public function get(): iterable
    {
        $file = $this->ftpClient->get($this->dsn, $this->options);

        if ($file !== null) {
            $message = $this->fileExecutor->execute($file);
        } else {
            $message = new Message("");
        }
        
        $envelope = new Envelope($message);

        return [$envelope];
    }

    public function ack(Envelope $envelope): void
    {
        $filesystem = $this->ftpClient->getFilesystem();
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
