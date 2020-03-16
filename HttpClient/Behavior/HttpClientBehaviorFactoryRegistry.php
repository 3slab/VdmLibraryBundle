<?php

namespace Vdm\Bundle\LibraryBundle\HttpClient\Behavior;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Vdm\Bundle\LibraryBundle\HttpClient\Behavior\HttpClientBehaviorFactoryInterface;

class HttpClientBehaviorFactoryRegistry
{
    /** 
     * @var HttpClientInterface $httpClient
    */
    private $httpClient;

    /** 
     * @var HttpClientBehaviorFactoryInterface[] $httpClientBehavior
    */
    private $httpClientBehavior;

    public function __construct()
    {
        $this->httpClientBehavior = [];
    }

    public function addFactory(HttpClientBehaviorFactoryInterface $httpClientBehavior, string $priority)
    {
        $this->httpClientBehavior[$priority] = $httpClientBehavior;
        ksort($this->httpClientBehavior);
    }

    public function create($httpClient, array $options)
    {
        $this->httpClient = $httpClient;

        foreach ($this->httpClientBehavior as $httpClientBehavior) {
            if ($httpClientBehavior->support($options)) {
                $this->httpClient = $httpClientBehavior->createDecoratedHttpClient($this->httpClient, $options);
            }
        }

        return $this->httpClient;
    }
}
