<?php

namespace Vdm\Bundle\LibraryBundle\Client\Elastic\Behavior;

use Psr\Log\LoggerInterface;
use Vdm\Bundle\LibraryBundle\Client\Elastic\ElasticClientInterface;

interface ElasticClientBehaviorFactoryInterface
{
    public static function priority(int $priority = 0);

    public function createDecoratedElasticClient(LoggerInterface $logger, ElasticClientInterface $elasticClient, array $options);

    public function support(array $options);
}
