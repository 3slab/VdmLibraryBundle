<?php

namespace Vdm\Bundle\LibraryBundle\HttpClient;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Symfony\Contracts\HttpClient\ResponseStreamInterface;

abstract class DecoratorHttpClient implements HttpClientInterface
{
    /** 
     * @var HttpClientInterface $httpClient
    */
    protected $httpClientDecorated;

    public function __construct(HttpClientInterface $httpClient) {
        $this->httpClientDecorated = $httpClient;
    }
    
    public function request(string $method, string $url, array $options = []): ResponseInterface
    {
        $this->httpClientDecorated->request($method, $url, $options);
    }

    public function stream($responses, float $timeout = null): ResponseStreamInterface
    {
        return $this->httpClientDecorated->stream($responses, $timeout);
    }
}
