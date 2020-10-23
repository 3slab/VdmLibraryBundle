<?php

/**
 * @package    3slab/VdmLibraryDoctrineOrmTransportBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryDoctrineOrmTransportBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Transport\Manual;

use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Transport\TransportInterface;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;

interface VdmManualExecutorInterface extends TransportInterface
{
    /**
     * @return string
     */
    public function getCode(): string;

    /**
     * @param SerializerInterface $serializer
     * @return void
     */
    public function setTransportSerializer(SerializerInterface $serializer): void;

    /**
     * @param LoggerInterface $logger
     * @return void
     */
    public function setTransportLogger(LoggerInterface $logger): void;
}