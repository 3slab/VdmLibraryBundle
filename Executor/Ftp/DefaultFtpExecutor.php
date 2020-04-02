<?php

namespace Vdm\Bundle\LibraryBundle\Executor\Ftp;

use Psr\Log\LoggerInterface;
use Vdm\Bundle\LibraryBundle\Model\Message;

class DefaultFtpExecutor implements FtpExecutorInterface
{
    /** 
     * @var LoggerInterface 
    */
    private $logger;

    public function __construct(LoggerInterface $logger) 
    {
        $this->logger = $logger;
    }

    public function execute(array $file): Message
    {
        // Get a message from "sftp"
        $this->logger->debug('Init Ftp Client...');

        $message = new Message($file['content']);
        unset($file['content']);
        $message->setMetadatas($file);

        return $message;
    }
}
