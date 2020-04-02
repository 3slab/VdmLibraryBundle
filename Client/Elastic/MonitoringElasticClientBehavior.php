<?php

namespace Vdm\Bundle\LibraryBundle\Client\Elastic;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Envelope;
use Vdm\Bundle\LibraryBundle\Client\Elastic\ElasticClientInterface;
use Vdm\Bundle\LibraryBundle\Client\Elastic\Event\ElasticClientErrorEvent;
use Vdm\Bundle\LibraryBundle\Client\Elastic\Event\ElasticClientReceivedEvent;

class MonitoringElasticClientBehavior extends DecoratorElasticClient
{
    /**
     * @var LoggerInterface $logger
     */
    protected $logger;

    /**
     * @var ElasticClientInterface $elasticClient
     */
    protected $elasticClient;

    /**
     * @var EventDispatcherInterface $eventDispatcher
     */
    private $eventDispatcher;

    /**
     * MonitoringElasticClientBehavior constructor
     */
    public function __construct(
        LoggerInterface $logger, 
        ElasticClientInterface $elasticClient, 
        EventDispatcherInterface $eventDispatcher
    )
    {
        parent::__construct($logger, $elasticClient);
        $this->eventDispatcher = $eventDispatcher;
    }
    
    /**
     * {@inheritDoc}
     */
    public function post(Envelope $envelope, string $index): ?array
    {        
        return null;
    }
}
