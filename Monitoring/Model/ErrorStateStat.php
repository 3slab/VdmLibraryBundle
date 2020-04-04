<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Monitoring\Model;

class ErrorStateStat
{
    /**
     * @var int
     */
    protected $code;

    /**
     * ErrorStateStat constructor.
     * @param int $code
     */
    public function __construct(int $code = 0)
    {
        $this->code = $code;
    }

    /**
     * @return int
     */
    public function getCode(): int
    {
        return $this->code;
    }
}
