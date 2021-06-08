<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle /blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Transport\Local;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Messenger\Exception\InvalidArgumentException;
use Symfony\Component\Messenger\Transport\TransportFactoryInterface;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;
use Symfony\Component\Messenger\Transport\TransportInterface;

class VdmLocalTransportFactory implements TransportFactoryInterface
{
    protected const DSN_PROTOCOL_LOCAL = 'vdm+local://';

    /**
     * @var LoggerInterface|null $logger
     */
    protected $logger;

    /**
     * @param LoggerInterface|null $vdmLogger
     */
    public function __construct(LoggerInterface $vdmLogger = null)
    {
        $this->logger = $vdmLogger ?? new NullLogger();
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
        $file = str_replace(self::DSN_PROTOCOL_LOCAL, '', $dsn);

        return new VdmLocalTransport(new Filesystem(), $file, $serializer, $this->logger);
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
        return strpos($dsn, self::DSN_PROTOCOL_LOCAL) === 0;
    }
}
