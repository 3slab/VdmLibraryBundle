<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Monitoring\Model;

class FtpClientResponseStat
{
    /**
     * @var int
     */
    protected $size;

    /**
     * FtpClientResponseStat constructor.
     *
     * @param int|null $size
     */
    public function __construct(?int $size = null)
    {
        $this->size = $size;
    }

    /**
     * @return int|null
     */
    public function getSize(): ?int
    {
        return $this->size;
    }
}
