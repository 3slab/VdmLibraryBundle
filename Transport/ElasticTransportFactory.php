<?php

namespace Vdm\Bundle\LibraryBundle\Transport;

use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;
use Symfony\Component\Messenger\Transport\TransportFactoryInterface;
use Symfony\Component\Messenger\Transport\TransportInterface;
use Vdm\Bundle\LibraryBundle\FileExecutor\EsFileExecutorInterface;
use Vdm\Bundle\LibraryBundle\EsClient\Behavior\EsClientBehaviorFactoryRegistry;
use Vdm\Bundle\LibraryBundle\EsClient\EsClientFactoryInterface;
use Vdm\Bundle\LibraryBundle\EsClient\EsClientInterface;

class ElasticTransportFactory implements TransportFactoryInterface
{
    private const DSN_PROTOCOL_ES = 'elasticsearch://';

    private const DSN_PROTOCOLS = [
        self::DSN_PROTOCOL_ES
    ];

    /**
     * @var LoggerInterface $logger
     */
    private $logger;

    /**
     * @var EsClientFactoryInterface $esClientFactory
     */
    private $esClientFactory;

    /**
     * @var EsClientInterface $esClient
     */
    private $esClient;

    /**
     * @var EsClientBehaviorFactoryRegistry $esClientBehaviorFactoryRegistry
     */
    private $esClientBehaviorFactoryRegistry;

    public function __construct(
        LoggerInterface $logger, 
        EsClientFactoryInterface $esClientFactory,
        EsClientBehaviorFactoryRegistry $esClientBehaviorFactoryRegistry
    )
    {
        $this->logger = $logger;
        $this->esClientFactory = $esClientFactory;
        $this->esClientBehaviorFactoryRegistry = $esClientBehaviorFactoryRegistry;
    }

    public function createTransport(string $dsn, array $options, SerializerInterface $serializer): TransportInterface
    {
        $es_options = $options['es_conf'];

        $this->esClient = $this->esClientFactory->create($dsn, $es_options);
        $this->esClient = $this->esClientBehaviorFactoryRegistry->create($this->esClient, $options);

        return new ElasticTransport($this->logger, $this->esClient, $dsn, $es_options);
    }

    public function supports(string $dsn, array $options): bool
    {
        foreach (self::DSN_PROTOCOLS as $protocol) {
            if (0 === strpos($dsn, $protocol)) {
                return true;
            }
        }
        return false;
    }
}
