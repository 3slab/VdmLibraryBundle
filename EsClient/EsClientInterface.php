<?php

namespace Vdm\Bundle\LibraryBundle\EsClient;

use Symfony\Component\Messenger\Envelope;

interface EsClientInterface
{
    public function post(Envelope $envelope, string $index): ?array;
}
