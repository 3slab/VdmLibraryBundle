<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Monitoring;

use Vdm\Bundle\LibraryBundle\Monitoring\Model\ConsumerStat;
use Vdm\Bundle\LibraryBundle\Monitoring\Model\ErrorStateStat;
use Vdm\Bundle\LibraryBundle\Monitoring\Model\ProducedStat;
use Vdm\Bundle\LibraryBundle\Monitoring\Model\RunningStat;
use Psr\Log\LoggerInterface;
use Vdm\Bundle\LibraryBundle\Monitoring\Model\ElasticClientResponseStat;
use Vdm\Bundle\LibraryBundle\Monitoring\Model\ErrorStat;
use Vdm\Bundle\LibraryBundle\Monitoring\Model\MemoryStat;
use Vdm\Bundle\LibraryBundle\Monitoring\Model\StatModelInterface;
use Vdm\Bundle\LibraryBundle\Monitoring\Model\TimeStat;

class NullStatsStorage implements StatsStorageInterface
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var array
     */
    private $config;

    /**
     * @var string
     */
    private $appName;

    /**
     * NullStatsStorage constructor.
     * @param LoggerInterface $messengerLogger
     */
    public function __construct($config, string $appName, LoggerInterface $messengerLogger)
    {
        $this->logger = $messengerLogger;
        $this->config = $config;
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

    /**
     * {@inheritDoc}
     */
    public function sendRunningStat(RunningStat $runningStat)
    {
        $this->logger->debug('Null stats storage running stats {isRunning}',
            [
                'isRunning' => $runningStat->isRunning() ?: '0'
            ]
        );
    }

    /**
     * {@inheritDoc}
     */
    public function sendErrorStateStat(ErrorStateStat $errorStateStat)
    {
        $this->logger->debug('Null stats storage error state sent with code {code}',
            [
                'code' => $errorStateStat->getCode()
            ]
        );
    }

    /**
     * {@inheritDoc}
     */
    public function sendErrorStat(ErrorStat $errorStat)
    {
        $this->logger->debug('Null stats storage error stats {errors} errors',
            [
                'errors' => $errorStat->getError()
            ]
        );
    }
    
    /**
     * {@inheritDoc}
     */
    public function sendTimeStat(TimeStat $timeStat)
    {
        $this->logger->debug('Null stats storage time state sent with value {value} milliseconds',
            [
                'value' => $timeStat->getTime()
            ]
        );
    }

    /**
     * {@inheritDoc}
     */
    public function sendMemoryStat(MemoryStat $memoryStat)
    {
        $this->logger->debug('Null stats storage memory state sent with value {value} octets',
            [
                'value' => $memoryStat->getMemory()
            ]
        );
    }
    
    /**
     * {@inheritDoc}
     */
    public function sendStat(StatModelInterface $statModel)
    {
        $stats = $statModel->getStats();

        foreach($stats as $stat) {
            $label = 'vdm.metric.'.$stat->getLabel();

            $this->logger->debug("Null stats storage $label state sent with value {value} seconds",
                [
                    'value' => $stat,
                ]
            );            
        }
    }

    public function flush(bool $force = false)
    {
        $this->logger->debug('Null stats storage flushed');
    }

    public static function getType()
    {
        return 'null';
    }
}
