<?php

namespace Vdm\Bundle\LibraryBundle\EsClient;

interface EsClientFactoryInterface
{
    public function create(string $dsn, ?array $options): EsClient;
}
