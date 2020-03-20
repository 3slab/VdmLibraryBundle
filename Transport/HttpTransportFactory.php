<?php

namespace Vdm\Bundle\LibraryBundle\Transport;

use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;
use Symfony\Component\Messenger\Transport\TransportFactoryInterface;
use Symfony\Component\Messenger\Transport\TransportInterface;
use Vdm\Bundle\LibraryBundle\HttpClient\Behavior\HttpClientBehaviorFactoryRegistry;
use Vdm\Bundle\LibraryBundle\Monitoring\StatsStorageInterface;
use Vdm\Bundle\LibraryBundle\RequestExecutor\AbstractHttpRequestExecutor;

class HttpTransportFactory implements TransportFactoryInterface
{
    private const DSN_PROTOCOL_HTTP = 'http://';
    private const DSN_PROTOCOL_HTTP_SSL = 'https://';

    private const DSN_PROTOCOLS = [
        self::DSN_PROTOCOL_HTTP,
        self::DSN_PROTOCOL_HTTP_SSL
    ];

    /**
     * @var LoggerInterface $logger
     */
    private $logger;

    /**
     * @var AbstractHttpRequestExecutor $requestExecutor
     */
    private $requestExecutor;


    /**
     * @var HttpClientBehaviorFactoryRegistry $httpClientBehaviorFactoryRegistry
     */
    private $httpClientBehaviorFactoryRegistry;

    public function __construct(
        LoggerInterface $logger, 
        StatsStorageInterface $statsStorage, 
        AbstractHttpRequestExecutor $requestExecutor, 
        HttpClientBehaviorFactoryRegistry $httpClientBehaviorFactoryRegistry
    )
    {
        $this->logger = $logger;
        $this->statsStorage = $statsStorage;
        $this->requestExecutor = $requestExecutor;
        $this->httpClientBehaviorFactoryRegistry = $httpClientBehaviorFactoryRegistry;
    }

    public function createTransport(string $dsn, array $options, SerializerInterface $serializer): TransportInterface
    {
        $method = $options['method'];
        $http_options = $options['http_options'];

        $this->logger->debug('Create decorator');
        $httpClientDecorated = $this->httpClientBehaviorFactoryRegistry->create($this->requestExecutor->getHttpClient(), $options);
        $this->requestExecutor->setHttpClient($httpClientDecorated);
        $this->logger->debug('Set new decorator');

        return new HttpTransport($this->requestExecutor, $dsn, $method, $http_options);
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
