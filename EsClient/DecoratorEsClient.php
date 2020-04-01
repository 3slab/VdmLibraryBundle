<?php

namespace Vdm\Bundle\LibraryBundle\EsClient;

use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Envelope;
use Vdm\Bundle\LibraryBundle\EsClient\EsClientInterface;

abstract class DecoratorEsClient implements EsClientInterface
{
    /** 
     * @var LoggerInterface $logger
    */
    protected $logger;

    /** 
     * @var EsClientInterface $esClient
    */
    protected $esClientDecorated;

    public function __construct(LoggerInterface $logger, EsClientInterface $esClient) {
        $this->esClientDecorated = $esClient;
        $this->logger = $logger;
    }
    
    /**
     * {@inheritDoc}
     */
    public function post(Envelope $envelope, string $index): ?array
    {
        return $this->esClientDecorated->post($envelope, $index);
    }
}
