<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Monitoring;

use Vdm\Bundle\LibraryBundle\Monitoring\Model\ConsumerStat;
use Vdm\Bundle\LibraryBundle\Monitoring\Model\ElasticClientErrorStat;
use Vdm\Bundle\LibraryBundle\Monitoring\Model\ElasticClientResponseStat;
use Vdm\Bundle\LibraryBundle\Monitoring\Model\ErrorStat;
use Vdm\Bundle\LibraryBundle\Monitoring\Model\ErrorStateStat;
use Vdm\Bundle\LibraryBundle\Monitoring\Model\FtpClientErrorStat;
use Vdm\Bundle\LibraryBundle\Monitoring\Model\HttpClientResponseStat;
use Vdm\Bundle\LibraryBundle\Monitoring\Model\FtpClientResponseStat;
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

    public function sendHttpResponseStat(HttpClientResponseStat $httpResponseStat);

    public function sendFtpResponseStat(FtpClientResponseStat $ftpResponseStat);

    public function sendFtpErrorStat(FtpClientErrorStat $ftpErrorStat);

    public function sendElasticResponseStat(ElasticClientResponseStat $elasticResponseStat);

    public function flush(bool $force = false);

    public static function getType();
}
