<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Monitoring\Model;

class FtpClientErrorStat
{    
    /**
     * @var int
     */
    protected $error;

    /**
     * FtpClientErrorStat constructor.
     *
     * @param int $error
     */
    public function __construct(int $error)
    {
        $this->error = $error;
    }

    /**
     * @return int
     */
    public function getError(): int
    {
        return $this->error;
    }
}
