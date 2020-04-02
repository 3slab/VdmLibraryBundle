<?php

namespace Vdm\Bundle\LibraryBundle\Client\Elastic;

use Symfony\Component\Messenger\Envelope;

interface ElasticClientInterface
{
    public function post(Envelope $envelope, string $index): ?array;
}
