<?php

namespace Vdm\Bundle\LibraryBundle\RequestExecutor;

use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class DefaultRequestExecutor extends AbstractHttpRequestExecutor
{
    /** 
     * @var LoggerInterface 
    */
    private $logger;

    /** 
     * @var SerializerInterface 
    */
    private $serializer;

    public function __construct(
        LoggerInterface $logger,
        SerializerInterface $serializer,
        HttpClientInterface $httpClient
    ) 
    {
        parent::__construct($httpClient);
        $this->logger = $logger;
        $this->serializer = $serializer;
    }

    public function execute(string $dsn, string $method, array $options): string
    {
        // Get a message from "website"
        $this->logger->debug('Init Http Client...');
        $response = $this->httpClient->request($method, $dsn, $options);
        $this->logger->debug('Request exec...');

        return $response->getContent();
    }
}
