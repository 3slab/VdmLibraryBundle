<?php

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
