<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Tests\Fixtures\AppBundle\Message;

/**
 * Class NotIsEmptyMessage
 * @package Vdm\Bundle\LibraryBundle\Tests\Message
 */
class NotIsEmptyMessage
{
    public $isEmptyCalled = false;

    public function isEmpty()
    {
        $this->isEmptyCalled = true;
    }
}
