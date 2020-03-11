<?php

namespace Vdm\Bundle\LibraryBundle\Transport;

use Symfony\Component\Messenger\Envelope;

abstract class HttpTransportMonitoring extends HttpTransportConfiguration
{
    /** 
     * @var TransportInterface $httpTransportConfiguration
    */
    protected $httpTransportConfiguration;

    public function get(): iterable
    {
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
