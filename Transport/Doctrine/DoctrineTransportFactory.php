<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Transport\Doctrine;

use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\MongoDBBundle\ManagerRegistry as OdmManagerRegistry;
use Doctrine\Persistence\ManagerRegistry as OrmManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;
use Symfony\Component\Messenger\Transport\TransportFactoryInterface;
use Symfony\Component\Messenger\Transport\TransportInterface;
use Symfony\Component\Serializer\SerializerInterface as SymfonySerializer;
use Vdm\Bundle\LibraryBundle\Exception\Doctrine\UndefinedEntityException;
use Vdm\Bundle\LibraryBundle\Executor\Doctrine\AbstractDoctrineExecutor;
use Vdm\Bundle\LibraryBundle\Executor\Doctrine\DoctrineExecutorConfigurator;
use Vdm\Bundle\LibraryBundle\Transport\Doctrine\DoctrineSenderFactory;
use Vdm\Bundle\LibraryBundle\Transport\Doctrine\DoctrineTransport;

class DoctrineTransportFactory implements TransportFactoryInterface
{
    protected const DSN_PROTOCOL_DOCTRINE_ODM = 'vdm+doctrine_odm://';
    protected const DSN_PROTOCOL_DOCTRINE_ORM = 'vdm+doctrine_orm://';
    protected const DSN_PATTERN_MATCHING  = '/(?P<protocol>[^:]+:\/\/)(?P<connection>.*)/';

    /**
     * @var LoggerInterface $logger
     */
    protected $logger;

    /**
     * @var OdmManagerRegistry $doctrineOdm
     */
    protected $doctrineOdm;

    /**
     * @var OrmManagerRegistry $doctrineOrm
     */
    protected $doctrineOrm;

    /**
     * @var AbstractDoctrineExecutor $executor
     */
    protected $executor;

    /**
     * @param LoggerInterface          $logger
     * @param ManagerRegistry          $doctrine
     * @param AbstractDoctrineExecutor $executor
     * @param SymfonySerializer        $serializer
     */
    public function __construct(
        LoggerInterface $logger,
        OdmManagerRegistry $doctrineOdm,
        OrmManagerRegistry $doctrineOrm,
        AbstractDoctrineExecutor $executor,
        SymfonySerializer $serializer
    ) {
        $this->logger     = $logger;
        $this->doctrineOdm   = $doctrineOdm;
        $this->doctrineOrm   = $doctrineOrm;
        $this->executor   = $executor;
        $this->serializer = $serializer;
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
        
        $configurator = new DoctrineExecutorConfigurator($manager, $this->logger, $this->serializer, $options);
        $configurator->configure($this->executor);

        $doctrineSenderFactory = new DoctrineSenderFactory($this->executor, $this->logger);
        $doctrineSender        = $doctrineSenderFactory->createDoctrineSender();

        return new DoctrineTransport($doctrineSender, $this->logger);
    }

    /**
     * Tests if DSN is valid (protocol and valid Doctrine connection).
     *
     * @param string $dsn
     * @param array  $options
     *
     * @return bool
     */
    public function supports(string $dsn, array $options): bool
    {
        preg_match(static::DSN_PATTERN_MATCHING, $dsn, $match);

        if (0 === strpos($match['protocol'], static::DSN_PROTOCOL_DOCTRINE_ODM) 
        || 0 === strpos($match['protocol'], static::DSN_PROTOCOL_DOCTRINE_ORM)) {
            // No need to put it in a variable now. If the connection doesn't exist, Doctrine will throw an exception
            $this->getManager($dsn);

            // If we passe the if statement, and getManager(), we're good. 
            return true;
        }

        // Otherwise, tranport not supported.
        return false;
    }

    /**
     * Returns the manager from Doctrine registry.
     *
     * @param  string $dsn
     *
     * @throws InvalidArgumentException invalid connection
     *
     * @return ObjectManager
     */
    protected function getManager(string $dsn): ObjectManager
    {
        preg_match(static::DSN_PATTERN_MATCHING, $dsn, $match);
        
        $match['connection'] = $match['connection'] ?: 'default';

        if (0 === strpos($match['protocol'], static::DSN_PROTOCOL_DOCTRINE_ODM)) {
            $manager = $this->doctrineOdm->getManager($match['connection']);
        }else if (0 === strpos($match['protocol'], static::DSN_PROTOCOL_DOCTRINE_ORM)) {
            $manager = $this->doctrineOrm->getManager($match['connection']);
        }

        return $manager;
    }
}
