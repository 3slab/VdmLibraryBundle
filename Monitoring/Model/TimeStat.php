<?php

namespace Vdm\Bundle\LibraryBundle\Monitoring\Model;

class TimeStat
{
    /**
     * @var int
     */
    protected $time;

    /**
     * TimeStat constructor.
     *
     * @param int $time
     */
    public function __construct(int $time = 0)
    {
        $this->time = $time;
    }

    /**
     * @return int
     */
    public function getTime(): int
    {
        return $this->time;
    }
}
