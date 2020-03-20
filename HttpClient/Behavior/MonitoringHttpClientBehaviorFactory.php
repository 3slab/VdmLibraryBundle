<?php

namespace Vdm\Bundle\LibraryBundle\HttpClient\Behavior;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Vdm\Bundle\LibraryBundle\HttpClient\MonitoringHttpClientBehavior;

class MonitoringHttpClientBehaviorFactory implements HttpClientBehaviorFactoryInterface
{
    private $eventDispatcher;

    public function __construct(
        EventDispatcherInterface $eventDispatcher
    )
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public static function priority(int $priority = -100)
    {
        return $priority;
    }

    public function createDecoratedHttpClient(LoggerInterface $logger, HttpClientInterface $httpClient, array $options)
    {
        return new MonitoringHttpClientBehavior($logger, $httpClient, $this->eventDispatcher);
    }

    public function support(array $options)
    {
        if (isset($options['monitoring']['enabled']) && $options['monitoring']['enabled'] === true) {
            return true;
        }

        return false;
    }
}
