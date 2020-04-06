<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Client\Elastic;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Envelope;
use Vdm\Bundle\LibraryBundle\Client\Elastic\DecoratorElasticClient;
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
        try {
            $this->logger->info(sprintf('Trying push in elasticsearch in this index %s', $index));
            $response = $this->elasticClientDecorated->post($envelope, $index);
            $this->logger->info(sprintf('Request done with status: %s', $response['result']));

            $this->eventDispatcher->dispatch(new ElasticClientReceivedEvent($response));
        } catch(\Exception $exception) {
            $this->logger->error(sprintf('%s: %s', get_class($exception), $exception->getMessage()));

            throw $exception;
        }

        return $response;
    }
}
