<?php

namespace Vdm\Bundle\LibraryBundle\Monitoring;

use Vdm\Bundle\LibraryBundle\Monitoring\Model\ConsumerStat;
use Vdm\Bundle\LibraryBundle\Monitoring\Model\ErrorStateStat;
use Vdm\Bundle\LibraryBundle\Monitoring\Model\ProducedStat;
use Vdm\Bundle\LibraryBundle\Monitoring\Model\RunningStat;
use Psr\Log\LoggerInterface;
use Vdm\Bundle\LibraryBundle\Monitoring\Model\ErrorStat;
use Vdm\Bundle\LibraryBundle\Monitoring\Model\TimeStat;

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

    public function sendErrorStat(ErrorStat $errorStat)
    {
        $this->logger->debug('Null stats storage error stats {errors} errors',
            [
                'errors' => $errorStat->getError()
            ]
        );
    }
    
    public function sendTimeStat(TimeStat $timeStat)
    {
        $this->logger->debug('Null stats storage time state sent with value {value} milliseconds',
            [
                'value' => $timeStat->getTime()
            ]
        );

    }

    public function flush()
    {
        $this->logger->debug('Null stats storage flushed');
    }
}
