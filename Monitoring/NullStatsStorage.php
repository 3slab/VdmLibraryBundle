<?php


namespace App\Monitoring;


use App\Monitoring\Model\ConsumerStat;
use App\Monitoring\Model\ErrorStateStat;
use App\Monitoring\Model\ProducedStat;
use App\Monitoring\Model\RunningStat;
use Psr\Log\LoggerInterface;

class NullStatsStorage implements StatsStorageInterface
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * NullStatsStorage constructor.
     * @param LoggerInterface $messengerLogger
     */
    public function __construct(LoggerInterface $messengerLogger)
    {
        $this->logger = $messengerLogger;
    }

    /**
     * {@inheritDoc}
     */
    public function sendConsumerStat(ConsumerStat $consumerStat)
    {
        $this->logger->debug('Null stats storage consumer stats {consumed} consumed for {nbItem} items',
            [
                'consumed' => $consumerStat->getConsumed(),
                'nbItem' => $consumerStat->getNbItem()
            ]
        );
    }

    /**
     * {@inheritDoc}
     */
    public function sendProducedStat(ProducedStat $producedStat)
    {
        $this->logger->debug('Null stats storage produced stats {produced} produced',
            [
                'produced' => $producedStat->getProduced()
            ]
        );
    }

    public function sendRunningStat(RunningStat $runningStat)
    {
        $this->logger->debug('Null stats storage running stats {isRunning}',
            [
                'isRunning' => $runningStat->isRunning() ?: '0'
            ]
        );
    }

    public function sendErrorStateStat(ErrorStateStat $errorStateStat)
    {
        $this->logger->debug('Null stats storage error state sent with code {code}',
            [
                'code' => $errorStateStat->getCode()
            ]
        );

    }


    public function flush()
    {
        $this->logger->debug('Null stats storage flushed');
    }
}
