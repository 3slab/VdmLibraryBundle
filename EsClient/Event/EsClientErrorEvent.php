<?php

namespace Vdm\Bundle\LibraryBundle\EsClient\Event;

use Symfony\Contracts\EventDispatcher\Event;

class EsClientErrorEvent extends Event
{
    /**
     * EsClientErrorEvent constructor
     */
    public function __construct()
    {
    }

    public function getError()
    {
        return 1;
    }
}
