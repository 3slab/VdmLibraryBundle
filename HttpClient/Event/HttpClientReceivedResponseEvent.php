<?php

namespace Vdm\Bundle\LibraryBundle\HttpClient\Event;

use Symfony\Contracts\EventDispatcher\Event;
use Symfony\Contracts\HttpClient\ResponseInterface;

class HttpClientReceivedResponseEvent extends Event
{
    /**
     * @var ResponseInterface $response
     */
    private $response;

    public function __construct(ResponseInterface $response)
    {
        $this->response = $response;
    }

    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }
}
