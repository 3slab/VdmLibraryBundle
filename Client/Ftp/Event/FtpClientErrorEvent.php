<?php

namespace Vdm\Bundle\LibraryBundle\Client\Ftp\Event;

use Symfony\Contracts\EventDispatcher\Event;

class FtpClientErrorEvent extends Event
{
    /**
     * FtpClientErrorEvent constructor
     */
    public function __construct()
    {
    }

    public function getError()
    {
        return 1;
    }
}
