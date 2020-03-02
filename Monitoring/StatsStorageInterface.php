<?php

namespace App\Monitoring;

use App\Monitoring\Model\ConsumerStat;
use App\Monitoring\Model\ErrorStateStat;
use App\Monitoring\Model\ProducedStat;
use App\Monitoring\Model\RunningStat;

interface StatsStorageInterface
{
    public function sendConsumerStat(ConsumerStat $consumerStat);

    public function sendProducedStat(ProducedStat $producedStat);

    public function sendRunningStat(RunningStat $runningStat);

    public function sendErrorStateStat(ErrorStateStat $exitCodeStat);

    public function flush();
}
