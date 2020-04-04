<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Client\Elastic\Behavior;

use Psr\Log\LoggerInterface;
use Vdm\Bundle\LibraryBundle\Client\Elastic\ElasticClientInterface;
use Vdm\Bundle\LibraryBundle\Client\Elastic\Behavior\ElasticClientBehaviorFactoryInterface;

class ElasticClientBehaviorFactoryRegistry
{
    /** 
     * @var LoggerInterface $logger
    */
    private $logger;

    /** 
     * @var ElasticClientInterface $elasticClient
    */
    private $elasticClient;

    /** 
     * @var ElasticClientBehaviorFactoryInterface[] $elasticClientBehavior
    */
    private $elasticClientBehavior;

    public function __construct(LoggerInterface $messengerLogger)
    {
        $this->logger = $messengerLogger;
        $this->elasticClientBehavior = [];
    }

    public function addFactory(ElasticClientBehaviorFactoryInterface $elasticClientBehavior, string $priority)
    {
        $this->elasticClientBehavior[$priority] = $elasticClientBehavior;
        ksort($this->elasticClientBehavior);
    }

    public function create($elasticClient, array $options)
    {
        $this->elasticClient = $elasticClient;

        foreach ($this->elasticClientBehavior as $elasticClientBehavior) {
            if ($elasticClientBehavior->support($options)) {
                $this->elasticClient = $elasticClientBehavior->createDecoratedElasticClient($this->logger, $this->elasticClient, $options);
            }
        }

        return $this->elasticClient;
    }
}
