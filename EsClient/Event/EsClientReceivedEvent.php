<?php

namespace Vdm\Bundle\LibraryBundle\EsClient\Event;

use Symfony\Contracts\EventDispatcher\Event;

class EsClientReceivedEvent extends Event
{
    /**
     * @var array|null $file
     */
    private $file;

    /**
     * EsClientReceivedEvent constructor
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
