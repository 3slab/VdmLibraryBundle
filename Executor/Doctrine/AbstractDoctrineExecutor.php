<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Executor\Doctrine;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Psr\Log\LoggerInterface;
use Vdm\Bundle\LibraryBundle\Model\Message;

abstract class AbstractDoctrineExecutor
{
    public const SELECTION_MODE_IDENTIFER = 0b000;
    public const SELECTION_MODE_FILTER    = 0b001;

    /**
     * @var array
     */
    protected $fetchMode = [];

    /**
     * @var array
     */
    protected $identifiers = [];
    /**
     * @var array
     */
    protected $filters = [];

    /**
     * @var EntityRepository[]
     */
    protected $repositories = [];

    /** 
     * @var EntityManagerInterface $manager
    */
    protected $manager;

    /** 
     * @var LoggerInterface $logger
    */
    protected $logger;

    abstract public function execute(Message $message): void;

    public function getManager(): EntityManagerInterface
    {
        return $this->manager;
    }

    public function setManager(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param string $key
     * @param int    $fetchMode
     *
     * @return self
     */
    public function setFetchMode(string $key, int $fetchMode): self
    {
        $this->fetchMode[$key] = $fetchMode;

        return $this;
    }

    /**
     * @param int $fetchMode
     *
     * @return int
     */
    public function getFetchMode(string $key): int
    {
        return $this->fetchMode[$key];
    }

    /**
     * @param string $key
     * @param string $identifier
     *
     * @return self
     */
    public function setIdentifier(string $key, string $identifier): self
    {
        $this->identifiers[$key] = $identifier;

        return $this;
    }

    /**
     * @param string $identifier
     *
     * @return self
     */
    public function getIdentifier(string $identifier): string
    {
        return $this->identifiers[$key];
    }

    /**
     * @param string $key
     * @param array  $filters
     *
     * @return self
     */
    public function setFilters(string $key, array $filters): self
    {
        $this->filters[$key] = $filters;

        return $this;
    }

    /**
     * @param string $key
     *
     * @return array
     */
    public function getFilters(string $key): array
    {
        return $this->filters[$key];
    }

    /**
     * @param string           $key
     * @param EntityRepository $repository
     *
     * @return self
     */
    public function addRepository(string $key, EntityRepository $repository): self
    {
        $this->repositories[$key] = $repository;

        return $this;
    }

    /**
     * @param EntityRepository $repository
     *
     * @return self
     */
    public function getRepository(string $key): EntityRepository
    {
        return $this->repositories[$key];
    }

    /**
     * @param LoggerInterface $logger $logger
     *
     * @return self
     */
    public function setLogger(LoggerInterface $logger): self
    {
        $this->logger = $logger;

        return $this;
    }
}
