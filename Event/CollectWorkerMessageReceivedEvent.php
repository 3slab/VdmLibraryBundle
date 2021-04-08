<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Event;

use Symfony\Component\Messenger\Event\AbstractWorkerMessageEvent;

/**
 * Class CollectWorkerMessageReceivedEvent
 * @package Vdm\Bundle\LibraryBundle\Event
 */
final class CollectWorkerMessageReceivedEvent extends AbstractWorkerMessageEvent
{
    private $shouldHandle = true;

    public function shouldHandle(bool $shouldHandle = null): bool
    {
        if (null !== $shouldHandle) {
            $this->shouldHandle = $shouldHandle;
        }

        return $this->shouldHandle;
    }
}
