<?php

namespace Vdm\Bundle\LibraryBundle\Client\Elastic;

use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Envelope;
use Vdm\Bundle\LibraryBundle\Client\Elastic\ElasticClientInterface;

abstract class DecoratorElasticClient implements ElasticClientInterface
{
    /** 
     * @var LoggerInterface $logger
    */
    protected $logger;

    /** 
     * @var ElasticClientInterface $elasticClient
    */
    protected $elasticClientDecorated;

    public function __construct(LoggerInterface $logger, ElasticClientInterface $elasticClient) {
        $this->elasticClientDecorated = $elasticClient;
        $this->logger = $logger;
    }
    
    /**
     * {@inheritDoc}
     */
    public function post(Envelope $envelope, string $index): ?array
    {
        return $this->elasticClientDecorated->post($envelope, $index);
    }
}
