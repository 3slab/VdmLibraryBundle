<?php

namespace Vdm\Bundle\LibraryBundle\HttpClient;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Symfony\Component\HttpClient\Exception\TransportException;

class RetryHttpClientBehavior extends DecoratorHttpClient
{
    /**
     * @var int $count
     */
    public $count = 0;

    /** 
     * @var int $retry
    */
    protected $retry;

    /** 
     * @var int $timeBeforeRetry
    */
    protected $timeBeforeRetry;

    public function __construct(HttpClientInterface $httpClient, int $retry, int $timeBeforeRetry) {
        parent::__construct($httpClient);
        $this->retry = $retry;
        $this->timeBeforeRetry = $timeBeforeRetry;
    }

    public function request(string $method, string $url, array $options = []): ResponseInterface
    {
        try{
            $response = $this->httpClientDecorated->request($method, $url, $options);
            $statusCode = $response->getStatusCode();
            $this->count = 0;
        } catch(TransportException $transportException) {
            if ($this->count < $this->retry) {
                $this->count++;
                sleep($this->timeBeforeRetry*$this->count);
                $response = $this->request($method, $url, $options);
            }
        }

        return $response;
    }
}
