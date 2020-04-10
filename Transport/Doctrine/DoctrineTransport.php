<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Transport\Doctrine;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Transport\Sender\SenderInterface;
use Symfony\Component\Messenger\Transport\TransportInterface;
use Vdm\Bundle\LibraryBundle\Exception\Doctrine\ReceiverNotSupportedException;

class DoctrineTransport implements TransportInterface
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var DoctrineSender
     */
    protected $sender;

    /**
     * @param DoctrineClient   $client
     * @param LoggerInterface  $logger
     */
    public function __construct(DoctrineSender $sender, LoggerInterface $logger = null)
    {
        $this->logger = $logger ?? new NullLogger();
        $this->sender = $sender;
    }

    public function get(): iterable
    {
        throw new ReceiverNotSupportedException(sprintf('%s transport does not support receiving messages', __CLASS__));
    }

    public function ack(Envelope $envelope): void
    {
        throw new ReceiverNotSupportedException(sprintf('%s transport does not support receiving messages', __CLASS__));
    }

    public function reject(Envelope $envelope): void
    {
        throw new ReceiverNotSupportedException(sprintf('%s transport does not support receiving messages', __CLASS__));
    }

    /**
     * {@inheritdoc}
     */
    public function send(Envelope $envelope): Envelope
    {
        $this->sender->send($envelope->getMessage());

        die();

        return $envelope;
    }
}
