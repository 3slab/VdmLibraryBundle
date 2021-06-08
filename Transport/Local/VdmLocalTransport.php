<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle /blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Transport\Local;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\InvalidArgumentException;
use Symfony\Component\Messenger\Stamp\ErrorDetailsStamp;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;
use Symfony\Component\Messenger\Transport\TransportInterface;
use Vdm\Bundle\LibraryBundle\Stamp\StopAfterHandleStamp;
use Vdm\Bundle\LibraryBundle\Transport\TransportCollectableInterface;

class VdmLocalTransport implements TransportInterface, TransportCollectableInterface
{
    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var string|null
     */
    protected $file;

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * VdmManualTransport constructor.
     * @param Filesystem $filesystem
     * @param string|null $file
     * @param SerializerInterface $serializer
     * @param LoggerInterface|null $logger
     */
    public function __construct(
        Filesystem $filesystem,
        ?string $file,
        SerializerInterface $serializer,
        LoggerInterface $logger = null
    ) {
        $this->filesystem = $filesystem;
        $this->file = $file;
        $this->serializer = $serializer;
        $this->logger = $logger ?? new NullLogger();
    }

    /**
     * @inheritDoc
     */
    public function get(): iterable
    {
        if (is_null($this->file)) {
            throw new InvalidArgumentException("consumer transport local cannot have a null file");
        }

        if (!$this->filesystem->exists($this->file)) {
            throw new InvalidArgumentException("file $this->file does not exist to simulate consumption");
        }

        $data = json_decode(file_get_contents($this->file), true);
        $this->logger->debug("local transport get message {message}", ['message' => $data]);

        $envelope = $this->serializer->decode($data);
        $envelope = $envelope->with(new StopAfterHandleStamp());

        return [$envelope];
    }

    /**
     * @inheritDoc
     */
    public function ack(Envelope $envelope): void
    {
        $this->logger->debug("local transport ack message");
    }

    /**
     * @inheritDoc
     */
    public function reject(Envelope $envelope): void
    {
        $this->logger->debug("local transport reject message");
    }

    /**
     * @inheritDoc
     */
    public function send(Envelope $envelope): Envelope
    {
        $data = $this->serializer->encode($envelope);
        $this->logger->debug("local transport send message {message}", ['message' => $data]);

        $outputFile = $this->file;
        if ($envelope->last(ErrorDetailsStamp::class)) {
            $extension = pathinfo($this->file, PATHINFO_EXTENSION);
            $baseName = basename($this->file, ".{$extension}");
            $basePath = pathinfo($this->file, PATHINFO_DIRNAME) . DIRECTORY_SEPARATOR;
            $outputFile = "{$basePath}{$baseName}-failed.{$extension}";
        }

        if (!is_null($outputFile)) {
            $this->filesystem->dumpFile($outputFile, json_encode($data, JSON_PRETTY_PRINT));
            $this->filesystem->chmod($outputFile, 0777);
        }

        return $envelope;
    }
}
