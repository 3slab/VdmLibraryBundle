<?php

namespace Vdm\Bundle\LibraryBundle\EsClient\Behavior;

use Psr\Log\LoggerInterface;
use Vdm\Bundle\LibraryBundle\EsClient\EsClientInterface;

interface EsClientBehaviorFactoryInterface
{
    public static function priority(int $priority = 0);

    public function createDecoratedEsClient(LoggerInterface $logger, EsClientInterface $esClient, array $options);

    public function support(array $options);
}
