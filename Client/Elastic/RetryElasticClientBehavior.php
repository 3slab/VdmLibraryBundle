<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

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

    /** 
     * @var int $timeBeforeRetry
    */
    protected $timeBeforeRetry;

    public function __construct(LoggerInterface $logger, ElasticClientInterface $elasticClient, int $retry, int $timeBeforeRetry) {
        parent::__construct($logger, $elasticClient);
        $this->retry = $retry;
        $this->timeBeforeRetry = $timeBeforeRetry;
    }

    public function post(Envelope $envelope, string $index): ?array
    {
        try{
            $this->logger->info(sprintf('Trying push in elasticsearch in this index %s', $index));
            $response = $this->elasticClientDecorated->post($envelope, $index);
            $this->logger->info(sprintf('Request done with status: %s', $response['result']));
            $this->count = 0;
        } catch(\Exception $exception) {
            $this->logger->error(sprintf('%s: %s', get_class($exception), $exception->getMessage()));

            if ($this->count < $this->retry) {
                $this->count++;
                $this->logger->info(sprintf('Wait %d second before retry; number of retry: %d', $this->timeBeforeRetry*$this->count, $this->count));
                sleep($this->timeBeforeRetry*$this->count);
                $response = $this->post($envelope, $index);
            } else {
                $this->count = 0;
                
                throw $exception;
            }
        }

        return $response;
    }
}
