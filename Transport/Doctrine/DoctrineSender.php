<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Transport\Doctrine;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Vdm\Bundle\LibraryBundle\Executor\Doctrine\AbstractDoctrineExecutor;
use Vdm\Bundle\LibraryBundle\Model\Message;

class DoctrineSender
{
    /**
     * @var AbstractDoctrineExecutor
     */
    protected $executor;

    /**
     * @param AbstractDoctrineExecutor  $executor
     * @param LoggerInterface           $logger
     */
    public function __construct(
        AbstractDoctrineExecutor $executor,
        LoggerInterface $logger = null
    ) {
        $this->executor   = $executor;
        $this->logger     = $logger ?? new NullLogger();
    }

    /**
     * Boostraps the send process
     *
     * @param  Message $message
     *
     * @return void
     */
    public function send(Message $message): void
    {   
        var_dump("sending message");

        $entity = $message->getPayload();
        $this->executor->execute($message);
    }
}