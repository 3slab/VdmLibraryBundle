<?php

namespace Vdm\Bundle\LibraryBundle\Monitoring\Model;

class FtpClientResponseStat
{    
    /**
     * @var int
     */
    protected $error;

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
    public function __construct(?int $error = null, ?int $size = null)
    {
        $this->error = $error;
        $this->size = $size;
    }

    /**
     * @return int|null
     */
    public function getError(): ?int
    {
        return $this->error;
    }

    /**
     * @return int|null
     */
    public function getSize(): ?int
    {
        return $this->size;
    }
}
