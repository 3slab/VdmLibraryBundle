<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Tests\Fixtures\AppBundle\Message;

/**
 * Class NotTraceableMessage
 * @package Vdm\Bundle\LibraryBundle\Tests\Message
 */
class NotTraceableMessage
{
    /**
     * @var bool
     */
    public $isAddTraceCalled = false;

    /**
     * @param $trace
     */
    public function addTrace($trace)
    {
        $this->isAddTraceCalled = true;
    }
}