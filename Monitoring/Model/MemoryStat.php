<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Monitoring\Model;

class MemoryStat
{
    /**
     * @var int
     */
    protected $memory;

    /**
     * MemoryStat constructor.
     *
     * @param int $memory
     */
    public function __construct(int $memory = 0)
    {
        $this->memory = $memory;
    }

    /**
     * @return int
     */
    public function getMemory(): int
    {
        return $this->memory;
    }
}
