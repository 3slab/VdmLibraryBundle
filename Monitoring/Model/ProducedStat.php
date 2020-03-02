<?php

namespace App\Monitoring\Model;

class ProducedStat
{
    /**
     * @var int
     */
    protected $produced;

    /**
     * ProducedStat constructor.
     *
     * @param int $produced
     */
    public function __construct(int $produced = 0)
    {
        $this->produced = $produced;
    }

    /**
     * @return int
     */
    public function getProduced(): int
    {
        return $this->produced;
    }
}
