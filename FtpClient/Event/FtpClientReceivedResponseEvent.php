<?php

namespace Vdm\Bundle\LibraryBundle\FtpClient\Event;

use Symfony\Contracts\EventDispatcher\Event;

class FtpClientReceivedResponseEvent extends Event
{
    /**
     * @var array|null $file
     */
    private $file;
    
    public function __construct(?array $file)
    {
        $this->file = $file;
    }

    public function getFile()
    {
        return $this->file;
    }
}
