<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Transport\Doctrine;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use ReflectionClass;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Vdm\Bundle\LibraryBundle\Model\Message;

class DoctrineSender
{
    public const SELECTION_MODE_IDENTIFER = 0b000;
    public const SELECTION_MODE_FILTER    = 0b001;

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var EntityRepository
     */
    protected $repository;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var int
     */
    protected $fetchMode;

    /**
     * @var string
     */
    protected $identifier;

    /**
     * @var string
     */
    protected $entityFqcn = '';

    /**
     * @var array
     */
    protected $filters = [];

    /**
     * @param EntityManagerInterface  $entityManager
     * @param EntityRepository        $repository
     * @param array                   $options
     */
    public function __construct(EntityManagerInterface $entityManager, EntityRepository $repository, LoggerInterface $logger = null)
    {
        $this->entityManager = $entityManager;
        $this->repository    = $repository;
        $this->logger        = $logger ?? new NullLogger();
    }

    /**
     * Boostraps the send process
     *
     * @param  Message $message
     *
     * @return void
     */
    public function send(Message $message): void
    {
        $entity = $message->getPayload();
        $entity = $this->matchEntity($entity);

        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

    /**
     * Defines logic to try and fetch previously existing entity and merges it with the new one.
     *
     * @param  object $entity
     *
     * @return object|null
     */
    protected function matchEntity(object $entity): ?object
    {
        // We'll be using different methods according to the options passed to the transport
        if (static::SELECTION_MODE_IDENTIFER === $this->fetchMode) {
            $id            = $this->getIdentifier($entity);
            $matchedEntity = $this->repository->find($id);
        } else {
            $filters       = $this->getFilters($entity);
            $id            = json_encode($filters);
            $matchedEntity = $this->repository->findOneBy($filters);
        }

        if ($matchedEntity) {
            // If the entity already exist, merge it.
            $entity     = $this->merge($matchedEntity, $entity);
            $logMessage = 'Updating {entity} with identity {id}.';

            $this->logger->info('Updating {entity} with identity {id}.', [
                'entity' => $entity->getRefDemande(),
            ]);
        } else {
            // If entity was not found, then we just have to create it.
            $logMessage = 'Creating {entity} with identity {id}.';
        }

        // Log what happened, and return entity
        $this->logger->info($logMessage, [
            'entity' => get_class($entity),
            'id'     => $id,
        ]);

        return $entity;
    }

    /**
     * Merges older entity with values from the new one.
     *
     * @param  object $previousEntity
     * @param  object $newerEntity
     *
     * @return object
     */
    public function merge(object $previousEntity, object $newerEntity): object
    {
        $metadata      = $this->entityManager->getClassMetadata($this->entityFqcn);
        $mapping       = $metadata->getFieldNames();
        $identifierKey = array_search($metadata->getIdentifier(), $mapping, true);

        // Remove identifer because it usually doesn't have a setter.
        unset($mapping[$identifierKey]);

        $accessor = PropertyAccess::createPropertyAccessor();

        foreach($mapping as $property) {
            $newValue = $accessor->getValue($newerEntity, $property);
            $accessor->setValue($previousEntity, $property, $newValue);
        }

        return $previousEntity;
    }

    /**
     * When the selector is the natural identity of the entity, returns the identifier.
     *
     * @param  object $entity
     *
     * @return mixed
     */
    protected function getIdentifier(object $entity)
    {
        $accessor = PropertyAccess::createPropertyAccessor();

        return $accessor->getValue($entity, $this->identifier);
    }

    /**
     * When the selector involves several fields or an non-natural identity field (i.e. table's PK).
     *
     * @param  object $entity
     *
     * @return array
     */
    protected function getFilters(object $entity): array
    {
        $accessor = PropertyAccess::createPropertyAccessorBuilder()
            ->enableMagicCall()
            ->getPropertyAccessor()
        ;

        $filterValues = [];

        foreach ($this->filters as $propety => $getter) {
            $filterValues[] = $accessor->getValue($entity, $getter);
        }

        $filter = array_combine(array_keys($this->filters), $filterValues);

        return $filter;

    }

    /**
     * @param int $fetchMode
     *
     * @return self
     */
    public function setFetchMode(int $fetchMode): self
    {
        $this->fetchMode = $fetchMode;

        return $this;
    }

    /**
     * @param string $identifier
     *
     * @return self
     */
    public function setIdentifier(string $identifier): self
    {
        $this->identifier = $identifier;

        return $this;
    }

    /**
     * @param array $filters
     *
     * @return self
     */
    public function setFilters(array $filters): self
    {
        $this->filters = $filters;

        return $this;
    }

    /**
     * @param string $entityFqcn
     *
     * @return self
     */
    public function setEntityFqcn(string $entityFqcn): self
    {
        $this->entityFqcn = $entityFqcn;

        return $this;
    }
}