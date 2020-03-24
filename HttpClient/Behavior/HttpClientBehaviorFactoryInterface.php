<?php

namespace Vdm\Bundle\LibraryBundle\HttpClient\Behavior;

use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

interface HttpClientBehaviorFactoryInterface
{
    public static function priority(int $priority = 0);

    public function createDecoratedHttpClient(LoggerInterface $logger, HttpClientInterface $httpClient, array $options);

    public function support(array $options);
}
