<?php

namespace Vdm\Bundle\LibraryBundle\EsClient\Behavior;

use Psr\Log\LoggerInterface;
use Vdm\Bundle\LibraryBundle\EsClient\EsClientInterface;
use Vdm\Bundle\LibraryBundle\EsClient\RetryEsClientBehavior;

class RetryEsClientBehaviorFactory implements EsClientBehaviorFactoryInterface
{
    public static function priority(int $priority = 0)
    {
        return $priority;
    }

    public function createDecoratedEsClient(LoggerInterface $logger, EsClientInterface $esClient, array $options)
    {
        $number = 5;

        if (isset($options['retry']['number'])) {
            $number = $options['retry']['number'];
        }

        return new RetryEsClientBehavior($logger, $esClient, $number);
    }

    public function support(array $options)
    {
        if (isset($options['retry']['enabled']) && $options['retry']['enabled'] === true) {
            return true;
        }

        return false;
    }
}
