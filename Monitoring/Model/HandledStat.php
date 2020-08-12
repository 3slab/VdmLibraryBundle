<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Monitoring\Model;

class HandledStat
{
    /**
     * @var int
     */
    protected $handled;

    /**
     * ProducedStat constructor.
     *
     * @param int $handled
     */
    public function __construct(int $handled = 0)
    {
        $this->handled = $handled;
    }

    /**
     * @return int
     */
    public function getHandled(): int
    {
        return $this->handled;
    }
}
