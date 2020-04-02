<?php

namespace Vdm\Bundle\LibraryBundle\Client\Elastic;

interface ElasticClientFactoryInterface
{
    public function create(string $dsn, ?array $options): ElasticClient;
}
