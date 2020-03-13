<?php

namespace Vdm\Bundle\LibraryBundle\RequestExecutor;

use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class DefaultRequestExecutor implements HttpRequestExecutorInterface
{
    /** 
     * @var LoggerInterface 
    */
    private $logger;

    /** 
     * @var SerializerInterface 
    */
    private $serializer;

    /** 
     * @var HttpClientInterface $httpClient 
    */
    private $httpClient;

    public function __construct(
        LoggerInterface $messengerLogger,
        SerializerInterface $serializer,
        HttpClientInterface $httpClient
    ) {
        $this->logger = $messengerLogger;
        $this->serializer = $serializer;
        $this->httpClient = $httpClient;
    }

    public function execute(string $dsn, string $method, array $options): string
    {
        // Get a message from "website"
        $this->logger->info('Init Http Client...');
        $response = $this->httpClient->request($method, $dsn, $options);
        $this->logger->info('Request exec...');

        if (null === $response) {
            return [];
        }

        return $response->getContent();
    }
}
