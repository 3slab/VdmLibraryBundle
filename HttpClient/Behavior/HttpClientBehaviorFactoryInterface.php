<?php

namespace Vdm\Bundle\LibraryBundle\HttpClient\Behavior;

use Symfony\Contracts\HttpClient\HttpClientInterface;

interface HttpClientBehaviorFactoryInterface
{
    public static function priority(int $priority = 0);

    public function createDecoratedHttpClient(HttpClientInterface $httpClient, array $options);

    public function support(array $options);
}
