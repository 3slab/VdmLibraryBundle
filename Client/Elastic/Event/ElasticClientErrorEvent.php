<?php

namespace Vdm\Bundle\LibraryBundle\Client\Elastic\Event;

use Symfony\Contracts\EventDispatcher\Event;

class ElasticClientErrorEvent extends Event
{
    /**
     * ElasticClientErrorEvent constructor
     */
    public function __construct()
    {
    }

    public function getError()
    {
        return 1;
    }
}
