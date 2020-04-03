<?php

namespace Vdm\Bundle\LibraryBundle\Transport\Elastic;

use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Transport\TransportInterface;
use Vdm\Bundle\LibraryBundle\Client\Elastic\ElasticClientInterface;

class ElasticTransport implements TransportInterface
{
    /** 
     * @var LoggerInterface $logger
    */
    private $logger;

    /** 
     * @var ElasticClientInterface $elasticClient
    */
    private $elasticClient;

    /** 
     * @var array $options
    */
    private $options;

    public function __construct(
        LoggerInterface $logger, 
        ElasticClientInterface $elasticClient,
        string $dsn,
        array $options
    )
    {
        $this->logger = $logger;
        $this->elasticClient = $elasticClient;
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
        $this->elasticClient->post($envelope, $this->options['index']);

        return $envelope;
    }
}