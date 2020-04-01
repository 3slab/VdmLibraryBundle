<?php

namespace Vdm\Bundle\LibraryBundle\EsClient;

use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Envelope;
use Vdm\Bundle\LibraryBundle\EsClient\DecoratorEsClient;

class RetryEsClientBehavior extends DecoratorEsClient
{
    /**
     * @var int $count
     */
    public $count = 0;

    /** 
     * @var int $retry
    */
    protected $retry;

    public function __construct(LoggerInterface $logger, EsClientInterface $esClient, int $retry) {
        parent::__construct($logger, $esClient);
        $this->retry = $retry;
    }

    public function post(Envelope $envelope, string $index): ?array
    {
        return null;
    }
}
