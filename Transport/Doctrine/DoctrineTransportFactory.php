<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Transport\Doctrine;

use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;
use Symfony\Component\Messenger\Transport\TransportFactoryInterface;
use Symfony\Component\Messenger\Transport\TransportInterface;
use Vdm\Bundle\LibraryBundle\Transport\Doctrine\DoctrineSender;
use Vdm\Bundle\LibraryBundle\Transport\Doctrine\DoctrineSenderFactory;
use Vdm\Bundle\LibraryBundle\Transport\Doctrine\DoctrineTransport;

class DoctrineTransportFactory implements TransportFactoryInterface
{
    protected const DSN_PROTOCOL_DOCTRINE = 'vdm+doctrine://';

    /**
     * @var LoggerInterface $logger
     */
    protected $logger;

    /**
     * @var DoctrineSenderFactory $doctrineSenderFactory
     */
    protected $doctrineSenderFactory;

    /**
     * @param LoggerInterface        $logger
     * @param DoctrineSenderFactory  $doctrineSenderFactory
     */
    public function __construct(
        LoggerInterface $logger,
        DoctrineSenderFactory $doctrineSenderFactory
    ) {
        $this->logger                = $logger;
        $this->doctrineSenderFactory = $doctrineSenderFactory;
    }

    /**
     * Creates DoctrineTransport
     * @param  string              $dsn
     * @param  array               $options
     * @param  SerializerInterface $serializer
     *
     * @return TransportInterface
     */
    public function createTransport(string $dsn, array $options, SerializerInterface $serializer): TransportInterface
    {
        unset($options['transport_name']);

        $doctrineSender = $this->doctrineSenderFactory->createDoctrineSender($options);

        return new DoctrineTransport($doctrineSender, $this->logger);
    }

    public function supports(string $dsn, array $options): bool
    {
        return (0 === strpos($dsn, static::DSN_PROTOCOL_DOCTRINE));
    }
}
