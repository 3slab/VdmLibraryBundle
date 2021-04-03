<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Service;

use Throwable;

class StopWorkerService
{
    /**
     * @var bool $flag
     */
    private $flag;

    /**
     * @var Throwable|null
     */
    private $throwable;

    /**
     * StopWorkerService constructor.
     */
    public function __construct()
    {
        $this->flag = false;
        $this->throwable = null;
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

    /**
     * Get $throwable
     *
     * @return Throwable|null
     */
    public function getThrowable(): ?Throwable
    {
        return $this->throwable;
    }

    /**
     * Set $throwable
     *
     * @param Throwable|null $exception
     *
     * @return StopWorkerService
     */
    public function setThrowable(?Throwable $exception): self
    {
        $this->throwable = $exception;

        return $this;
    }
}