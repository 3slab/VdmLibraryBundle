<?php

namespace Vdm\Bundle\LibraryBundle\HttpClient;

use Symfony\Contracts\HttpClient\ResponseInterface;

class MonitoringHttpClientBehavior extends DecoratorHttpClient
{
    public function request(string $method, string $url, array $options = []): ResponseInterface
    {        
        $httpClient = $this->httpClientDecorated->request($method, $url, $options);

        return $httpClient;
    }
}
