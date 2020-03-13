<?php

namespace Vdm\Bundle\LibraryBundle\Transport;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Transport\TransportInterface;
use Vdm\Bundle\LibraryBundle\Model\Message;
use Vdm\Bundle\LibraryBundle\RequestExecutor\HttpRequestExecutorInterface;

class HttpTransport implements TransportInterface
{
    /** 
     * @var HttpRequestExecutorInterface $requestExecutor
    */
    private $requestExecutor;

    /** 
     * @var string $dsn
    */
    private $dsn;

    /** 
     * @var string $method
    */
    private $method;

    /** 
     * @var array $options
    */
    private $options;

    public function __construct(HttpRequestExecutorInterface $requestExecutor, string $dsn, string $method, array $options)
    {
        $this->requestExecutor = $requestExecutor;
        $this->dsn = $dsn;
        $this->method = $method;
        $this->options = $options;
    }

    public function get(): iterable
    {
        $content = $this->requestExecutor->execute($this->dsn, $this->method, $this->options);

        $message = new Message($content);
        $envelope = new Envelope($message);

        return [$envelope];
    }

    public function ack(Envelope $envelope): void
    {
    }

    public function reject(Envelope $envelope): void
    {
    }

    public function send(Envelope $envelope): Envelope
    {
    }
}
