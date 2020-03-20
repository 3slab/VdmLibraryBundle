<?php

namespace Vdm\Bundle\LibraryBundle\HttpClient\Behavior;

use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Vdm\Bundle\LibraryBundle\HttpClient\RetryHttpClientBehavior;

class RetryHttpClientBehaviorFactory implements HttpClientBehaviorFactoryInterface
{
    public static function priority(int $priority = 0)
    {
        return $priority;
    }

    public function createDecoratedHttpClient(LoggerInterface $logger, HttpClientInterface $httpClient, array $options)
    {
        $number = 5;
        $timeBeforeRetry = 5;

        if (isset($options['retry']['number'])) {
            $number = $options['retry']['number'];
        }
        if (isset($options['retry']['timeBeforeRetry'])) {
            $timeBeforeRetry = $options['retry']['timeBeforeRetry'];
        }

        return new RetryHttpClientBehavior($logger, $httpClient, $number, $timeBeforeRetry);
    }

    public function support(array $options)
    {
        if (isset($options['retry']['enabled']) && $options['retry']['enabled'] === true) {
            return true;
        }

        return false;
    }
}
