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
     * @var AbstractHttpExecutor $requestExecutor
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

    public function __construct(AbstractHttpExecutor $requestExecutor, string $dsn, string $method, array $options)
    {
        $this->requestExecutor = $requestExecutor;
        $this->dsn = $dsn;
        $this->method = $method;
        $this->options = $options;
    }

    public function get(): iterable
    {
        $content = $this->requestExecutor->execute($this->dsn, $this->method, $this->options);

        if ($content instanceof Envelope) {
            $content->with(new StopAfterHandleStamp());
            
            return [$content];
        }

        $message = new Message($content);
        $envelope = new Envelope($message, [new StopAfterHandleStamp()]);

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
