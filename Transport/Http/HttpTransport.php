<?php

namespace Vdm\Bundle\LibraryBundle\Transport\Http;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Transport\TransportInterface;
use Vdm\Bundle\LibraryBundle\Model\Message;
use Vdm\Bundle\LibraryBundle\Executor\Http\AbstractHttpExecutor;
use Vdm\Bundle\LibraryBundle\Stamp\StopAfterHandleStamp;

class HttpTransport implements TransportInterface
{
    /** 
     * @var AbstractHttpExecutor $httpExecutor
    */
    private $httpExecutor;

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

    public function __construct(AbstractHttpExecutor $httpExecutor, string $dsn, string $method, array $options)
    {
        $this->httpExecutor = $httpExecutor;
        $this->dsn = $dsn;
        $this->method = $method;
        $this->options = $options;
    }

    public function get(): iterable
    {
        return $this->httpExecutor->execute($this->dsn, $this->method, $this->options);
    }

    public function ack(Envelope $envelope): void
    {
    }

    public function reject(Envelope $envelope): void
    {
    }

    public function send(Envelope $envelope): Envelope
    {
        return $envelope;
    }
}
