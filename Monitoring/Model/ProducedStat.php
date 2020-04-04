<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Monitoring\Model;

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
