<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Tests\MessageHandler;

use PHPUnit\Framework\TestCase;
use Vdm\Bundle\LibraryBundle\MessageHandler\DefaultHandler;
use Vdm\Bundle\LibraryBundle\Model\Message;

/**
 * Class DefaultHandler
 *
 * @package Vdm\Bundle\LibraryBundle\MessageHandler
 */
class DefaultHandlerTest extends TestCase
{
    public function testGetHandledMessages()
    {
        $iterator = DefaultHandler::getHandledMessages();

        $this->assertArrayHasKey('priority', $iterator->current());
    }
}
