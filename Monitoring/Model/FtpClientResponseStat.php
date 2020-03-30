<?php

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
     * @param int|null $error
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
