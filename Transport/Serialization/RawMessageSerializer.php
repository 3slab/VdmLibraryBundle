<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Transport\Serialization;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\MessageDecodingFailedException;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;

/**
 * Class RawMessageSerializer
 * @package Vdm\Bundle\LibraryBundle\Transport\Serialization
 */
class RawMessageSerializer implements SerializerInterface
{
    /**
     * @var SerializerInterface|null
     */
    protected $serializer;

    /**
     * @var string|null
     */
    protected $className;

    /**
     * RawMessageSerializer constructor.
     * @param SerializerInterface|null $serializer
     * @param string|null $className
     */
    public function __construct(SerializerInterface $serializer = null, string $className = null)
    {
        $this->serializer = $serializer;
        $this->className = $className;
    }

    /**
     * {@inheritDoc}
     */
    public function decode(array $encodedEnvelope): Envelope
    {
        if (!class_exists($this->className)) {
            throw new MessageDecodingFailedException(
                "Class $this->className does not exists when attempting to decode raw message"
            );
        }

        $toDecodeEnvelope = [
            'body' => $encodedEnvelope,
            'headers' => ['type' => $this->className]
        ];

        return $this->serializer->decode($toDecodeEnvelope);
    }

    /**
     * {@inheritDoc}
     */
    public function encode(Envelope $envelope): array
    {
        return $this->serializer->encode($envelope)['body'];
    }
}
