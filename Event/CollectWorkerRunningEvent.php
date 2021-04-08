<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Event;

use Vdm\Bundle\LibraryBundle\Service\CollectWorker;

/**
 * Class CollectWorkerRunningEvent
 * @package Vdm\Bundle\LibraryBundle\Event
 */
final class CollectWorkerRunningEvent
{
    /**
     * @var CollectWorker
     */
    private $worker;

    /**
     * @var bool
     */
    private $isWorkerIdle;

    /**
     * CollectWorkerRunningEvent constructor.
     * @param CollectWorker $worker
     * @param bool $isWorkerIdle
     */
    public function __construct(CollectWorker $worker, bool $isWorkerIdle)
    {
        $this->worker = $worker;
        $this->isWorkerIdle = $isWorkerIdle;
    }

    /**
     * @return CollectWorker
     */
    public function getWorker(): CollectWorker
    {
        return $this->worker;
    }

    /**
     * Returns true when no message has been received by the worker.
     */
    public function isWorkerIdle(): bool
    {
        return $this->isWorkerIdle;
    }
}
