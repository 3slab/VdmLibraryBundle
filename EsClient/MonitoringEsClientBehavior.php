<?php

namespace Vdm\Bundle\LibraryBundle\EsClient;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Envelope;
use Vdm\Bundle\LibraryBundle\EsClient\EsClientInterface;
use Vdm\Bundle\LibraryBundle\EsClient\Event\EsClientErrorEvent;
use Vdm\Bundle\LibraryBundle\EsClient\Event\EsClientReceivedEvent;

class MonitoringEsClientBehavior extends DecoratorEsClient
{
    /**
     * @var LoggerInterface $logger
     */
    protected $logger;

    /**
     * @var EsClientInterface $esClient
     */
    protected $esClient;

    /**
     * @var EventDispatcherInterface $eventDispatcher
     */
    private $eventDispatcher;

    /**
     * MonitoringEsClientBehavior constructor
     */
    public function __construct(
        LoggerInterface $logger, 
        EsClientInterface $esClient, 
        EventDispatcherInterface $eventDispatcher
    )
    {
        parent::__construct($logger, $esClient);
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
