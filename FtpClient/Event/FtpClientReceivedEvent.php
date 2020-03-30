<?php

namespace Vdm\Bundle\LibraryBundle\FtpClient\Event;

use Symfony\Contracts\EventDispatcher\Event;

class FtpClientReceivedEvent extends Event
{
    /**
     * @var array|null $file
     */
    private $file;

    /**
     * FtpClientReceivedEvent constructor
     */
    public function __construct(?array $file)
    {
        $this->file = $file;
    }

    /**
     * @return array|null
     */
    public function getFile(): ?array
    {
        return $this->file;
    }
}
