<?php

namespace Vdm\Bundle\LibraryBundle\Client\Http;

use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Symfony\Contracts\HttpClient\ResponseStreamInterface;

abstract class DecoratorHttpClient implements HttpClientInterface
{
    /** 
     * @var LoggerInterface $logger
    */
    protected $logger;

    /** 
     * @var HttpClientInterface $httpClient
    */
    protected $httpClientDecorated;

    public function __construct(LoggerInterface $logger, HttpClientInterface $httpClient) {
        $this->httpClientDecorated = $httpClient;
        $this->logger = $logger;
    }
    
    /**
     * {@inheritDoc}
     */
    public function request(string $method, string $url, array $options = []): ResponseInterface
    {
        return $this->httpClientDecorated->request($method, $url, $options);
    }

    /**
     * {@inheritDoc}
     */
    public function stream($responses, float $timeout = null): ResponseStreamInterface
    {
        return $this->httpClientDecorated->stream($responses, $timeout);
    }
}