<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Event;

use Vdm\Bundle\LibraryBundle\Service\CollectWorker;

/**
 * Class CollectWorkerStartedEvent
 * @package Vdm\Bundle\LibraryBundle\Event
 */
final class CollectWorkerStartedEvent
{
    /**
     * @var CollectWorker
     */
    private $worker;

    /**
     * CollectWorkerStartedEvent constructor.
     * @param CollectWorker $worker
     */
    public function __construct(CollectWorker $worker)
    {
        $this->worker = $worker;
    }

    /**
     * @return CollectWorker
     */
    public function getWorker(): CollectWorker
    {
        return $this->worker;
    }
}
