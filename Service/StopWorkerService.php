<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Service;

class StopWorkerService
{
    /**
     * @var bool $flag
     */
    private $flag;

    /**
     * StopWorkerService constructor.
     */
    public function __construct()
    {
        $this->flag = false;
    }

    /**
     * Get $flag
     */
    public function getFlag(): bool
    {
        return $this->flag;
    }

    /**
     * Set $flag
     * 
     * @var bool $flag
     * 
     * @return StopWorkerService
     */
    public function setFlag(bool $flag): self
    {
        $this->flag = $flag;

        return $this;
    }
}