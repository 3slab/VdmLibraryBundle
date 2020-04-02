<?php

namespace Vdm\Bundle\LibraryBundle\Executor\Http;

use Symfony\Contracts\HttpClient\HttpClientInterface;

abstract class AbstractHttpExecutor
{
    /** 
     * @var HttpClientInterface $httpClient
    */
    protected $httpClient;

    public function __construct(HttpClientInterface $httpClient) {
        $this->httpClient = $httpClient;
    }

    abstract public function execute(string $dsn, string $method, array $options): iterable;

    public function getHttpClient(): HttpClientInterface
    {
        return $this->httpClient;
    }

    public function setHttpClient(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }
}
