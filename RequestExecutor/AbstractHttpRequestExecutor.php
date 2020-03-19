<?php

namespace Vdm\Bundle\LibraryBundle\RequestExecutor;

use Symfony\Contracts\HttpClient\HttpClientInterface;

abstract class AbstractHttpRequestExecutor
{
    /** 
     * @var HttpClientInterface $httpClient
    */
    protected $httpClient;

    public function __construct(HttpClientInterface $httpClient) {
        $this->httpClient = $httpClient;
    }

    abstract public function execute(string $dsn, string $method, array $options): string;

    public function getHttpClient(): HttpClientInterface
    {
        return $this->httpClient;
    }

    public function setHttpClient(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }
}
