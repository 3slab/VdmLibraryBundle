<?php

namespace Vdm\Bundle\LibraryBundle\EsClient\Behavior;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Vdm\Bundle\LibraryBundle\EsClient\EsClientInterface;
use Vdm\Bundle\LibraryBundle\EsClient\MonitoringEsClientBehavior;

class MonitoringEsClientBehaviorFactory implements EsClientBehaviorFactoryInterface
{
    private $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public static function priority(int $priority = -100)
    {
        return $priority;
    }

    public function createDecoratedEsClient(LoggerInterface $logger, EsClientInterface $esClient, array $options)
    {
        return new MonitoringEsClientBehavior($logger, $esClient, $this->eventDispatcher);
    }

    public function support(array $options)
    {
        if (isset($options['monitoring']['enabled']) && $options['monitoring']['enabled'] === true) {
            return true;
        }

        return false;
    }
}
