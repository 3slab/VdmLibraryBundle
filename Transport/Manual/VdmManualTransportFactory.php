<?php

/**
 * @package    3slab/VdmLibraryDoctrineOrmTransportBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryDoctrineOrmTransportBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Transport\Manual;

use Psr\Log\LoggerInterface;
use Psr\Container\ContainerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Symfony\Component\Messenger\Transport\TransportFactoryInterface;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;
use Symfony\Component\Messenger\Transport\TransportInterface;

class VdmManualTransportFactory implements TransportFactoryInterface
{
    protected const DSN_PROTOCOL_MANUAL = 'vdm+manual://';

    /**
     * @var LoggerInterface|null $logger
     */
    protected $logger;

    /**
     * @var VdmManualExecutorCollection
     */
    protected $executors;

    /**
     * @param VdmManualExecutorCollection $executors
     * @param LoggerInterface|null $logger
     */
    public function __construct(
        VdmManualExecutorCollection $executors,
        LoggerInterface $logger = null
    )
    {
        $this->executors = $executors;
        $this->logger = $logger ?? new NullLogger();
    }

    /**
     * Creates ManualTransport
     * @param string $dsn
     * @param array $options
     * @param SerializerInterface $serializer
     *
     * @return TransportInterface
     */
    public function createTransport(string $dsn, array $options, SerializerInterface $serializer): TransportInterface
    {
        $executor = $this->getExecutor($dsn, $serializer);
        return new VdmManualTransport($executor, $this->logger);
    }

    /**
     * Tests if DSN is supported by transport
     *
     * @param string $dsn
     * @param array $options
     *
     * @return bool
     */
    public function supports(string $dsn, array $options): bool
    {
        return strpos($dsn, self::DSN_PROTOCOL_MANUAL) === 0;
    }

    /**
     * Returns the manager from Doctrine registry.
     *
     * @param string $dsn
     * @param SerializerInterface $serializer
     * @return VdmManualExecutorInterface
     */
    protected function getExecutor(string $dsn, SerializerInterface $serializer): VdmManualExecutorInterface
    {
        $executorServiceId = str_replace(self::DSN_PROTOCOL_MANUAL, '', $dsn);
        if (!$this->executors->has($executorServiceId)) {
            throw new ServiceNotFoundException(sprintf('Service %s not found when attempting to create messenger transport %s', $executorServiceId, $dsn));
        }

        $executor = $this->executors->get($executorServiceId);
        if (!$executor instanceof VdmManualExecutorInterface) {
            throw new InvalidConfigurationException(sprintf('Service %s does not implements interface VdmManualExecutorInterface', $executorServiceId));
        }

        $executor->setTransportSerializer($serializer);
        $executor->setTransportLogger($this->logger);

        return $executor;
    }
}