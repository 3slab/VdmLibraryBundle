<?php


namespace App\Monitoring\Model;


class RunningStat
{
    /**
     * @var bool
     */
    protected $running;

    /**
     * RunningStat constructor.
     * @param bool $running
     */
    public function __construct(bool $running)
    {
        $this->running = $running;
    }

    /**
     * @return bool
     */
    public function isRunning(): bool
    {
        return $this->running;
    }
}