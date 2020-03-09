<?php

namespace Vdm\Bundle\LibraryBundle\Monitoring;

use Vdm\Bundle\LibraryBundle\Monitoring\Model\ConsumerStat;
use Vdm\Bundle\LibraryBundle\Monitoring\Model\ErrorStat;
use Vdm\Bundle\LibraryBundle\Monitoring\Model\ErrorStateStat;
use Vdm\Bundle\LibraryBundle\Monitoring\Model\MemoryStat;
use Vdm\Bundle\LibraryBundle\Monitoring\Model\ProducedStat;
use Vdm\Bundle\LibraryBundle\Monitoring\Model\RunningStat;
use Vdm\Bundle\LibraryBundle\Monitoring\Model\TimeStat;

interface StatsStorageInterface
{
    public function sendConsumerStat(ConsumerStat $consumerStat);

    public function sendProducedStat(ProducedStat $producedStat);

    public function sendRunningStat(RunningStat $runningStat);

    public function sendErrorStateStat(ErrorStateStat $exitCodeStat);

    public function sendErrorStat(ErrorStat $errorStat);
    
    public function sendTimeStat(TimeStat $timeStat);

    public function sendMemoryStat(MemoryStat $timeStat);

    public function flush(bool $force = false);

    public static function getType();
}
