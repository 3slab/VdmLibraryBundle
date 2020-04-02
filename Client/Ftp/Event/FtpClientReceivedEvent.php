<?php

namespace Vdm\Bundle\LibraryBundle\Client\Ftp\Event;

use Symfony\Contracts\EventDispatcher\Event;

class FtpClientReceivedEvent extends Event
{
    /**
     * @var string|null $content
     */
    private $content;

    /**
     * FtpClientReceivedEvent constructor
     */
    public function __construct(?string $content)
    {
        $this->content = $content;
    }

    /**
     * @return string|null
     */
    public function getContent(): ?string
    {
        return $this->content;
    }
}
