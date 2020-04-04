<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */


namespace Vdm\Bundle\LibraryBundle\Monitoring\Model;


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