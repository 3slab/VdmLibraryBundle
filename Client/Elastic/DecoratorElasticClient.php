<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Client\Elastic;

use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Envelope;
use Vdm\Bundle\LibraryBundle\Client\Elastic\ElasticClientInterface;

abstract class DecoratorElasticClient implements ElasticClientInterface
{
    /** 
     * @var LoggerInterface $logger
    */
    protected $logger;

    /** 
     * @var ElasticClientInterface $elasticClient
    */
    protected $elasticClientDecorated;

    public function __construct(LoggerInterface $logger, ElasticClientInterface $elasticClient) {
        $this->elasticClientDecorated = $elasticClient;
        $this->logger = $logger;
    }
    
    /**
     * {@inheritDoc}
     */
    public function post(Envelope $envelope, string $index): ?array
    {
        return $this->elasticClientDecorated->post($envelope, $index);
    }
}
