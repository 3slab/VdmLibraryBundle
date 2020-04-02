<?php

namespace Vdm\Bundle\LibraryBundle\Transport;

use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Transport\TransportInterface;
use Vdm\Bundle\LibraryBundle\EsClient\EsClientInterface;

class ElasticTransport implements TransportInterface
{
    /** 
     * @var LoggerInterface $logger
    */
    private $logger;

    /** 
     * @var EsClientInterface $esClient
    */
    private $esClient;

    /** 
     * @var array $options
    */
    private $options;

    public function __construct(
        LoggerInterface $logger, 
        EsClientInterface $esClient,
        string $dsn,
        array $options
    )
    {
        $this->logger = $logger;
        $this->esClient = $esClient;
        $this->dsn = $dsn;
        $this->options = $options;
    }

    public function get(): iterable
    {
        return [];
    }

    public function ack(Envelope $envelope): void
    {
    }

    public function reject(Envelope $envelope): void
    {        
    }

    public function send(Envelope $envelope): Envelope
    {
        $this->esClient->post($envelope, $this->options['index']);

        return $envelope;
    }
}
