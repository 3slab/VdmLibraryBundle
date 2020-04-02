<?php

namespace Vdm\Bundle\LibraryBundle\EsClient\Behavior;

use Psr\Log\LoggerInterface;
use Vdm\Bundle\LibraryBundle\EsClient\EsClientInterface;
use Vdm\Bundle\LibraryBundle\EsClient\Behavior\EsClientBehaviorFactoryInterface;

class EsClientBehaviorFactoryRegistry
{
    /** 
     * @var LoggerInterface $logger
    */
    private $logger;

    /** 
     * @var EsClientInterface $esClient
    */
    private $esClient;

    /** 
     * @var EsClientBehaviorFactoryInterface[] $esClientBehavior
    */
    private $esClientBehavior;

    public function __construct(LoggerInterface $messengerLogger)
    {
        $this->logger = $messengerLogger;
        $this->esClientBehavior = [];
    }

    public function addFactory(EsClientBehaviorFactoryInterface $esClientBehavior, string $priority)
    {
        $this->esClientBehavior[$priority] = $esClientBehavior;
        ksort($this->esClientBehavior);
    }

    public function create($esClient, array $options)
    {
        $this->esClient = $esClient;

        foreach ($this->esClientBehavior as $esClientBehavior) {
            if ($esClientBehavior->support($options)) {
                $this->esClient = $esClientBehavior->createDecoratedEsClient($this->logger, $this->esClient, $options);
            }
        }

        return $this->esClient;
    }
}
