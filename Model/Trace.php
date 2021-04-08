<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Model;

/**
 * Class Trace
 *
 * @package Vdm\Bundle\LibraryBundle\Model
 */
class Trace
{
    public const ENTER = 'enter';
    public const EXIT = 'exit';

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $event;

    /**
     * @var float
     */
    private $timestamp;

    /**
     * Trace constructor.
     *
     * @param string $name
     * @param string $event
     * @param float $timestamp
     */
    public function __construct(string $name, string $event, float $timestamp = null)
    {
        $this->name = $name;
        $this->event = $event;
        $this->timestamp = $timestamp ?: microtime(true);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getEvent(): string
    {
        return $this->event;
    }

    /**
     * @return float
     */
    public function getTimestamp(): float
    {
        return $this->timestamp;
    }
}
