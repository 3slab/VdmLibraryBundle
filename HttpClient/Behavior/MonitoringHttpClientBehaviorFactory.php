<?php

namespace Vdm\Bundle\LibraryBundle\HttpClient\Behavior;

use Vdm\Bundle\LibraryBundle\HttpClient\MonitoringHttpClientBehavior;

class MonitoringHttpClientBehaviorFactory implements HttpClientBehaviorFactoryInterface
{
    public static function priority(int $priority = 0)
    {
        return $priority;
    }

    public function createDecoratedHttpClient($httpClient, array $options)
    {
        return new MonitoringHttpClientBehavior($httpClient, $options);
    }

    public function support(array $options)
    {
        return false;
    }
}
