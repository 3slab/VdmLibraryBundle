<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Transport\Doctrine;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;
use Symfony\Component\Messenger\Transport\TransportFactoryInterface;
use Symfony\Component\Messenger\Transport\TransportInterface;
use Vdm\Bundle\LibraryBundle\Exception\Doctrine\UndefinedEntityException;
use Vdm\Bundle\LibraryBundle\Executor\Doctrine\AbstractDoctrineExecutor;
use Vdm\Bundle\LibraryBundle\Executor\Doctrine\DoctrineExecutorConfigurator;
use Vdm\Bundle\LibraryBundle\Transport\Doctrine\DoctrineSender;
use Vdm\Bundle\LibraryBundle\Transport\Doctrine\DoctrineSenderFactory;
use Vdm\Bundle\LibraryBundle\Transport\Doctrine\DoctrineTransport;

class DoctrineTransportFactory implements TransportFactoryInterface
{
    protected const DSN_PROTOCOL_DOCTRINE = 'vdm+doctrine://';
    protected const DSN_PATTERN_MATCHING  = '/(?P<protocol>[^:]+:\/\/)(?P<connection>.*)/';

    /**
     * @var LoggerInterface $logger
     */
    protected $logger;

    /**
     * @var ManagerRegistry $doctrine
     */
    protected $doctrine;

    /**
     * @var AbstractDoctrineExecutor $executor
     */
    protected $executor;

    /**
     * @param LoggerInterface               $logger
     * @param ManagerRegistry               $doctrine
     * @param AbstractDoctrineExecutor      $executor
     */
    public function __construct(
        LoggerInterface $logger,
        ManagerRegistry $doctrine,
        AbstractDoctrineExecutor $executor
    ) {
        $this->logger   = $logger;
        $this->doctrine = $doctrine;
        $this->executor = $executor;
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
        if (empty($options['entities'])) {
            $errorMessage = sprintf('%s requires that you define at least one entity value in the transport\'s options.', __CLASS__);
            throw new UndefinedEntityException($errorMessage);
        }

        unset($options['transport_name']);

        $manager      = $this->getManager($dsn);
        $configurator = new DoctrineExecutorConfigurator($manager, $this->logger, $options);
        $configurator->configure($this->executor);

        $doctrineSenderFactory = new DoctrineSenderFactory($this->executor, $this->logger);
        $doctrineSender        = $doctrineSenderFactory->createDoctrineSender();

        return new DoctrineTransport($doctrineSender, $this->logger);
    }

    /**
     * Tests if DSN is valid (protocol and valid Doctrine connection).
     *
     * @param  string $dsn
     * @param  array  $options
     *
     * @return bool
     */
    public function supports(string $dsn, array $options): bool
    {
        preg_match(static::DSN_PATTERN_MATCHING, $dsn, $match);

        // No need to put it in a variable now. If the connection doesn't exist, Doctrine will throw an exception
        $this->getManager($dsn);

        return (0 === strpos($match['protocol'], static::DSN_PROTOCOL_DOCTRINE));
    }

    /**
     * Returns the manager from Doctrine registry.
     *
     * @param  string $dsn
     *
     * @throws InvalidArgumentException invalid connection
     *
     * @return EntityManagerInterface
     */
    protected function getManager(string $dsn): EntityManagerInterface
    {
        preg_match(static::DSN_PATTERN_MATCHING, $dsn, $match);

        $match['connection'] = $match['connection'] ?: 'default';

        return $this->doctrine->getManager($match['connection']);
    }
}
