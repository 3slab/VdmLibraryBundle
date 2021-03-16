<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Model;

/**
 * interface TraceableMessageInterface
 *
 * @package Vdm\Bundle\LibraryBundle\Model
 */
interface TraceableMessageInterface
{
    /**
     * @return Trace[]
     */
    public function getTraces(): array;

    /**
     * @param Trace[] $traces
     */
    public function setTraces(array $traces): void;

    /**
     * @param Trace $trace
     */
    public function addTrace(Trace $trace): void;

    /**
     * @return Trace|null
     */
    public function getLastTrace(): ?Trace;
}
