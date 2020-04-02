<?php

namespace Vdm\Bundle\LibraryBundle\Client\Elastic\Event;

use Symfony\Contracts\EventDispatcher\Event;

class ElasticClientReceivedEvent extends Event
{
    /**
     * @var array|null $file
     */
    private $file;

    /**
     * ElasticClientReceivedEvent constructor
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
