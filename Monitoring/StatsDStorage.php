<?php

namespace Vdm\Bundle\LibraryBundle\Monitoring;

use DataDog\BatchedDogStatsd;
use DataDog\DogStatsd;
use Vdm\Bundle\LibraryBundle\Monitoring\Model\ConsumerStat;
use Vdm\Bundle\LibraryBundle\Monitoring\Model\ErrorStateStat;
use Vdm\Bundle\LibraryBundle\Monitoring\Model\ProducedStat;
use Vdm\Bundle\LibraryBundle\Monitoring\Model\RunningStat;
use Vdm\Bundle\LibraryBundle\Monitoring\Model\ErrorStat;
use Vdm\Bundle\LibraryBundle\Monitoring\Model\FtpClientResponseStat;
use Vdm\Bundle\LibraryBundle\Monitoring\Model\HttpClientResponseStat;
use Vdm\Bundle\LibraryBundle\Monitoring\Model\MemoryStat;
use Vdm\Bundle\LibraryBundle\Monitoring\Model\TimeStat;

class StatsDStorage implements StatsStorageInterface
{
    /**
     * @var array
     */
    private $config;

    /**
     * @var DogStatsd
     */
    private $datadog;

    /**
     * @var string
     */
    private $appName;

    public function __construct($config = 'datadog:', string $appName)
    {
        if (false === class_exists(DogStatsd::class)) {
            throw new \LogicException('Seems client library is not installed. Please install "datadog/php-datadogstatsd"');
        }

        $this->appName = $appName;
        $this->config = $this->prepareConfig($config);
        
        if (null === $this->datadog) {
            if (true === filter_var($this->config['batched'], FILTER_VALIDATE_BOOLEAN)) {
                $this->datadog = new BatchedDogStatsd($this->config);
            } else {
                $this->datadog = new DogStatsd($this->config);
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function sendConsumerStat(ConsumerStat $consumerStat)
    {
        $this->datadog->histogram('vdm.metric.consumer.consume.counter', $consumerStat->getConsumed());
        $this->datadog->histogram('vdm.metric.consumer.number.counter', $consumerStat->getNbItem());
    }

    public function sendProducedStat(ProducedStat $producedStat)
    {
        $this->datadog->increment('vdm.metric.producer.counter', $producedStat->getProduced());
    }

    public function sendRunningStat(RunningStat $runningStat)
    {
        $this->datadog->gauge('vdm.metric.running', $runningStat->isRunning());
    }

    public function sendErrorStateStat(ErrorStateStat $errorStateStat)
    {
        $this->datadog->gauge('vdm.metric.error.state', $errorStateStat->getCode());
    }

    public function sendErrorStat(ErrorStat $errorStat)
    {
        $this->datadog->increment('vdm.metric.error.counter', $errorStat->getError());
    }
    
    public function sendTimeStat(TimeStat $timeStat)
    {
        $this->datadog->gauge('vdm.metric.time', $timeStat->getTime());
    }
    
    public function sendMemoryStat(MemoryStat $memoryStat)
    {
        $this->datadog->gauge('vdm.metric.memory', $memoryStat->getMemory());
    }
    
    public function sendHttpResponseStat(HttpClientResponseStat $httpResponseStat)
    {
        $tags = [
            "statusCode" => $httpResponseStat->getStatusCode()
        ];

        if ($httpResponseStat->getTime() !== null) {
            $this->datadog->gauge('vdm.metric.http.response_time', $httpResponseStat->getTime());
        }
        if ($httpResponseStat->getBodySize() !== null) {
            $this->datadog->gauge('vdm.metric.http.body_size', $httpResponseStat->getBodySize());
        }
        $this->datadog->increment('vdm.metric.http.status_code.counter', 1, $tags);
    }

    public function sendFtpResponseStat(FtpClientResponseStat $ftpResponseStat)
    {
        if ($ftpResponseStat->getError() !== null) {
            $this->datadog->increment('vdm.metric.ftp.error.counter', 1);
        }
        if ($ftpResponseStat->getSize() !== null) {
            $this->datadog->gauge('vdm.metric.ftp.size', $ftpResponseStat->getSize());
        }
    }
    
    public function flush(bool $force = false)
    {
    }

    /**
     * @param $config
     *
     * @return array
     */
    private function prepareConfig($config): array
    {
        $config['global_tags'] = [
            'appName' => $this->appName,
        ];

        $hostname = getenv('HOSTNAME');
        if ($hostname !== false) {
            $config['global_tags']['hostname'] = $hostname;
        }
        
        return array_replace([
            'host' => 'localhost',
            'port' => 9125,
            'batched' => false,
            'global_tags' => [],
        ], $config);
    }

    public static function getType()
    {
        return 'statsd';
    }
}