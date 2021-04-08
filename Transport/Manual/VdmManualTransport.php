<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle /blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Transport\Manual;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\TransportException;
use Symfony\Component\Messenger\Transport\TransportInterface;
use Vdm\Bundle\LibraryBundle\Transport\TransportCollectableInterface;

class VdmManualTransport implements TransportInterface, TransportCollectableInterface
{
    /**
     * @var VdmManualExecutorInterface
     */
    protected $executor;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * VdmManualTransport constructor.
     * @param VdmManualExecutorInterface $executor
     * @param LoggerInterface|null $vdmLogger
     */
    public function __construct(VdmManualExecutorInterface $executor, LoggerInterface $vdmLogger = null)
    {
        $this->executor = $executor;
        $this->logger = $vdmLogger ?? new NullLogger();
    }

    /**
     * @inheritDoc
     */
    public function get(): iterable
    {
        return $this->executor->get();
    }

    /**
     * @inheritDoc
     */
    public function ack(Envelope $envelope): void
    {
        $this->executor->ack($envelope);
    }

    /**
     * @inheritDoc
     */
    public function reject(Envelope $envelope): void
    {
        $this->executor->reject($envelope);
    }

    /**
     * @inheritDoc
     */
    public function send(Envelope $envelope): Envelope
    {
        return $this->executor->send($envelope);
    }
}
