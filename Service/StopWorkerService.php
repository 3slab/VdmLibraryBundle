<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Service;

use Exception;

class StopWorkerService
{
    /**
     * @var bool $flag
     */
    private $flag;

    /**
     * @var Exception|null
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
     * @return Exception|null
     */
    public function getThrowable(): ?Exception
    {
        return $this->throwable;
    }

    /**
     * Set $throwable
     *
     * @param Exception|null $exception
     *
     * @return StopWorkerService
     */
    public function setThrowable(?Exception $exception): self
    {
        $this->throwable = $exception;

        return $this;
    }
}