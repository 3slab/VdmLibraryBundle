<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

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
