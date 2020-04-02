<?php

namespace Vdm\Bundle\LibraryBundle\Executor\Ftp;

use Symfony\Component\Messenger\Envelope;
use Vdm\Bundle\LibraryBundle\Client\Ftp\FtpClientInterface;
use Vdm\Bundle\LibraryBundle\Model\Message;

abstract class AbstractFtpExecutor
{
    /** 
     * @var FtpClientInterface $ftpClient
    */
    protected $ftpClient;

    public function __construct() {
    }

    public function execute(array $options): array
    {
        $files = $this->ftpClient->list($options['dirpath']);
        $envelope = [];

        foreach ($files as $file) {
            if (isset($file) && $file['type'] === 'file') {
                $content = $this->ftpClient->get($options['dirpath'].'/'.$file['basename']);
                $message = new Message($content);
                $message->setMetadatas($file);
                $envelope[] = new Envelope($message);
            }
        }

        if (count($envelope) === 0) {
            $message = new Message("");
            $envelope[] = new Envelope($message);
        }

        return $envelope;
    }

    public function getFtpClient(): FtpClientInterface
    {
        return $this->ftpClient;
    }

    public function setFtpClient(FtpClientInterface $ftpClient)
    {
        $this->ftpClient = $ftpClient;
    }
}
