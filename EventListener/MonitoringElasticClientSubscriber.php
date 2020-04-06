<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\EventListener;

use Vdm\Bundle\LibraryBundle\Monitoring\StatsStorageInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Psr\Log\LoggerInterface;
use Vdm\Bundle\LibraryBundle\Client\Elastic\Event\ElasticClientReceivedEvent;
use Vdm\Bundle\LibraryBundle\Monitoring\Model\ElasticClientResponseStat;

class MonitoringElasticClientSubscriber implements EventSubscriberInterface
{
    /**
     * @var StatsStorageInterface
     */
    private $storage;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * MonitoringElasticClientSubscriber constructor.
     *
     * @param StatsStorageInterface $storage
     * @param LoggerInterface|null $messengerLogger
     */
    public function __construct(StatsStorageInterface $storage, LoggerInterface $messengerLogger = null)
    {
        $this->storage = $storage;
        $this->logger = $messengerLogger;
    }

    /**
     * Method executed on ElasticClientReceivedEvent event
     *
     * @param ElasticClientReceivedEvent $event
     */
    public function onElasticClientReceivedEvent(ElasticClientReceivedEvent $event)
    {
        $response = $event->getElasticResponse();
        
        $success = $response['_shards']['successful'];
        $index = $response['_index'];
        $resultat = $response['result'];

        $this->logger->debug(sprintf('successful: %d', $success));
        $this->logger->debug(sprintf('index: %s', $index));
        $this->logger->debug(sprintf('resultat: %s', $resultat));

        $elasticClientResponseStat = new ElasticClientResponseStat($success, $index, $resultat);
        $this->storage->sendElasticResponseStat($elasticClientResponseStat);
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            ElasticClientReceivedEvent::class => 'onElasticClientReceivedEvent',
        ];
    }
}
