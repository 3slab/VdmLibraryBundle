<?php

namespace Vdm\Bundle\LibraryBundle\EventListener;

use Vdm\Bundle\LibraryBundle\Monitoring\StatsStorageInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Psr\Log\LoggerInterface;
use Vdm\Bundle\LibraryBundle\Client\Http\Event\HttpClientReceivedResponseEvent;
use Vdm\Bundle\LibraryBundle\Monitoring\Model\HttpClientResponseStat;

class MonitoringHttpClientSubscriber implements EventSubscriberInterface
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
     * MonitoringHttpClientSubscriber constructor.
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
     * Method executed on HttpClientReceivedResponseEvent event
     *
     * @param HttpClientReceivedResponseEvent $event
     */
    public function onHttpClientReceivedResponseEvent(HttpClientReceivedResponseEvent $event)
    {
        $response = $event->getResponse();
        $statusCode = $response->getStatusCode();
        
        $responseInfo = $response->getInfo();
        
        $bodySize = $responseInfo['size_download'];
        $time = $responseInfo['total_time'];
        
        $this->logger->debug(sprintf('statusCode: %s', $statusCode));
        $this->logger->debug(sprintf('bodySize: %d', $bodySize));
        $this->logger->debug(sprintf('execution time: %.2f', $time));

        $httpClientResponseStat = new HttpClientResponseStat($time , $bodySize, $statusCode);
        $this->storage->sendHttpResponseStat($httpClientResponseStat);
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            HttpClientReceivedResponseEvent::class => 'onHttpClientReceivedResponseEvent',
        ];
    }
}
