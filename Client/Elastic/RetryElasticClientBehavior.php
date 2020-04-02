<?php

namespace Vdm\Bundle\LibraryBundle\Client\Elastic;

use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Envelope;
use Vdm\Bundle\LibraryBundle\Client\Elastic\DecoratorElasticClient;

class RetryElasticClientBehavior extends DecoratorElasticClient
{
    /**
     * @var int $count
     */
    public $count = 0;

    /** 
     * @var int $retry
    */
    protected $retry;

    public function __construct(LoggerInterface $logger, ElasticClientInterface $elasticClient, int $retry) {
        parent::__construct($logger, $elasticClient);
        $this->retry = $retry;
    }

    public function post(Envelope $envelope, string $index): ?array
    {
        return null;
    }
}
