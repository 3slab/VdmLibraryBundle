<?php

/**
 * @package    3slab/VdmLibraryDoctrineOrmTransportBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryDoctrineOrmTransportBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Tests\Transport\Manual;

use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;
use Vdm\Bundle\LibraryBundle\Transport\Manual\VdmManualExecutorInterface;

/**
 * Class MyManualExecutor
 *
 * @package Vdm\Bundle\LibraryBundle\Tests\Transport\Manuel
 */
class MyManualExecutor implements VdmManualExecutorInterface
{
    public $logger;

    public $serializer;

    public $getCalled = 0;

    public $ackCalled = 0;

    public $rejectCalled = 0;

    public $sendCalled = 0;

    public function get(): iterable
    {
        $this->getCalled++;
        return [];
    }

    public function ack(Envelope $envelope): void
    {
        $this->ackCalled++;
    }

    public function reject(Envelope $envelope): void
    {
        $this->rejectCalled++;
    }

    public function send(Envelope $envelope): Envelope
    {
        $this->sendCalled++;
        return new Envelope(new \stdClass());
    }

    public function getCode(): string
    {
        return 'my_manual_executor';
    }

    public function setTransportSerializer(SerializerInterface $serializer): void
    {
        $this->serializer = $serializer;
    }

    public function setTransportLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }
}
