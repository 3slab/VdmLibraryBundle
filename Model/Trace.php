<?php

namespace Vdm\Bundle\LibraryBundle\Model;

/**
 * Class Trace
 *
 * @package Vdm\Bundle\LibraryBundle\Model
 */
class Trace
{
    CONST ENTER = 'enter';
    const EXIT = 'exit';

    /**
     * @var string
     */
    private $app;

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
     * @param string $app
     * @param string $event
     * @param float $timestamp
     */
    public function __construct(string $app, string $event, float $timestamp = null)
    {
        $this->app = $app;
        $this->event = $event;
        $this->timestamp = $timestamp ?: microtime(true);
    }

    /**
     * @return string
     */
    public function getApp(): string
    {
        return $this->app;
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
