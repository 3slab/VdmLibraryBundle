<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Event;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Event\AbstractWorkerMessageEvent;

/**
 * Class CollectWorkerMessageFailedEvent
 * @package Vdm\Bundle\LibraryBundle\Event
 */
final class CollectWorkerMessageFailedEvent extends AbstractWorkerMessageEvent
{
    /**
     * @var \Throwable
     */
    private $throwable;

    /**
     * @var bool
     */
    private $willRetry = false;

    /**
     * CollectWorkerMessageFailedEvent constructor.
     * @param Envelope $envelope
     * @param string $receiverName
     * @param \Throwable $error
     */
    public function __construct(Envelope $envelope, string $receiverName, \Throwable $error)
    {
        $this->throwable = $error;

        parent::__construct($envelope, $receiverName);
    }

    /**
     * @return \Throwable
     */
    public function getThrowable(): \Throwable
    {
        return $this->throwable;
    }

    /**
     * @return bool
     */
    public function willRetry(): bool
    {
        return $this->willRetry;
    }

    public function setForRetry(): void
    {
        $this->willRetry = true;
    }
}
