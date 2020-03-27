<?php

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