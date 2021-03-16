<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Model;

/**
 * trait TraceableTrait
 *
 * @package Vdm\Bundle\LibraryBundle\Model
 */
trait TraceableTrait
{
    /**
     * @var Trace[]
     */
    protected $traces = [];

    /**
     * @return Trace[]
     */
    public function getTraces(): array
    {
        return $this->traces;
    }

    /**
     * @param Trace[] $traces
     */
    public function setTraces(array $traces): void
    {
        $this->traces = $traces;
    }

    /**
     * @param Trace $trace
     */
    public function addTrace(Trace $trace): void
    {
        $this->traces[] = $trace;
    }

    /**
     * @return Trace|null
     */
    public function getLastTrace(): ?Trace
    {
        return current(array_slice($this->getTraces(), -1)) ?: null;
    }
}
