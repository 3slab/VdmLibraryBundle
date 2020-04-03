<?php

namespace Vdm\Bundle\LibraryBundle\Client\Elastic\Behavior;

use Psr\Log\LoggerInterface;
use Vdm\Bundle\LibraryBundle\Client\Elastic\ElasticClientInterface;
use Vdm\Bundle\LibraryBundle\Client\Elastic\RetryElasticClientBehavior;

class RetryElasticClientBehaviorFactory implements ElasticClientBehaviorFactoryInterface
{
    public static function priority(int $priority = 0)
    {
        return $priority;
    }

    public function createDecoratedElasticClient(LoggerInterface $logger, ElasticClientInterface $elasticClient, array $options)
    {
        $number = 5;

        if (isset($options['retry']['number'])) {
            $number = $options['retry']['number'];
        }

        if (isset($options['retry']['timeBeforeRetry'])) {
            $timeBeforeRetry = $options['retry']['timeBeforeRetry'];
        }

        return new RetryElasticClientBehavior($logger, $elasticClient, $number, $timeBeforeRetry);
    }

    public function support(array $options)
    {
        if (isset($options['retry']['enabled']) && $options['retry']['enabled'] === true) {
            return true;
        }

        return false;
    }
}
