<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Tests\Message;

use PHPUnit\Framework\TestCase;
use Vdm\Bundle\LibraryBundle\Message\DefaultMessage;
use Vdm\Bundle\LibraryBundle\MessageHandler\DefaultHandler;
use Vdm\Bundle\LibraryBundle\Model\Message;

/**
 * Class DefaultMessage
 *
 * @package Vdm\Bundle\LibraryBundle\Message
 */
class DefaultMessageTest extends TestCase
{
    public function testDefaultMessae()
    {
        $message = new DefaultMessage();

        $this->assertInstanceOf(Message::class, $message);
    }
}
