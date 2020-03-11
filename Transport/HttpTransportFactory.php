<?php

namespace Vdm\Bundle\LibraryBundle\Transport;

use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\Messenger\Transport\Receiver\ReceiverInterface;
use Symfony\Component\Messenger\Transport\Sender\SenderInterface;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;
use Symfony\Component\Messenger\Transport\TransportFactoryInterface;
use Symfony\Component\Messenger\Transport\TransportInterface;
use Vdm\Bundle\LibraryBundle\RequestExecutor\HttpRequestExecutorInterface;

class HttpTransportFactory implements TransportFactoryInterface
{
    private const DSN_PROTOCOL_HTTP = 'http://';
    private const DSN_PROTOCOL_HTTP_SSL = 'https://';

    private const DSN_PROTOCOLS = [
        self::DSN_PROTOCOL_HTTP,
        self::DSN_PROTOCOL_HTTP_SSL
    ];

    /**
     * @var HttpRequestExecutorInterface $requestExecutor
     */
    private $requestExecutor;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function createTransport(string $dsn, array $options, SerializerInterface $serializer): TransportInterface
    {
        $method = $options['method'];
        $http_options = $options['http_options'];

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

    public function setRequestExecutor(HttpRequestExecutorInterface $requestExecutor)
    {
        $this->requestExecutor = $requestExecutor;
    }
}
