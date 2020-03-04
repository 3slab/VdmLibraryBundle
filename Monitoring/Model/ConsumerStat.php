<?php

namespace Vdm\Bundle\LibraryBundle\Monitoring\Model;

class ConsumerStat
{
    /**
     * @var int
     */
    protected $consumed;

    /**
     * @var int
     */
    protected $nbItem;

    /**
     * ConsumerStat constructor.
     *
     * @param int $consumed
     * @param int $nbItem
     */
    public function __construct(int $consumed = 0, int $nbItem = 0)
    {
        $this->consumed = $consumed;
        $this->nbItem = $nbItem;
    }

    /**
     * @return int
     */
    public function getConsumed(): int
    {
        return $this->consumed;
    }

    /**
     * @return int
     */
    public function getNbItem(): int
    {
        return $this->nbItem;
    }
}
