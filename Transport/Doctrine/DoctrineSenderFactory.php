<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Transport\Doctrine;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use ReflectionClass;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Vdm\Bundle\LibraryBundle\Exception\Doctrine\InvalidIdentifiersCountException;
use Vdm\Bundle\LibraryBundle\Exception\Doctrine\UndefinedEntityException;
use Vdm\Bundle\LibraryBundle\Exception\Doctrine\UnreadableEntityPropertyException;
use Vdm\Bundle\LibraryBundle\Transport\Doctrine\DoctrineSender;

class DoctrineSenderFactory
{
    /**
     * @var array
     */
    protected $options = [];

    /**
     * @var array
     */
    protected $filters = [];

    /**
     * @var int
     */
    protected $fetchMode;

    /**
     * @var string
     */
    protected $identifier = '';

    /**
     * @var PropertyAccessor
     */
    protected $accessor;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager,
        LoggerInterface $logger = null
    ) {
        $this->entityManager = $entityManager;
        $this->logger        = $logger ?? new NullLogger();
    }

    /**
     * Created the DoctrineSender object based on messenger configuration.
     *
     * @param  array  $options
     *
     * @return DoctrineSender
     */
    public function createDoctrineSender(array $options): DoctrineSender
    {
        if (empty($options['entity'])) {
            $errorMessage = sprintf('%s requires that you define an entity value in the transport\'s options.', __CLASS__);
            throw new UndefinedEntityException($errorMessage);
        }

        $this->options  = $options;
        $this->accessor = PropertyAccess::createPropertyAccessorBuilder()
            ->enableMagicCall()
            ->getPropertyAccessor()
        ;

        $repository = $this->entityManager->getRepository($options['entity']);
        $sender     = new DoctrineSender($this->entityManager, $repository, $this->logger);

        // Reads or guess how to access the entity's identity
        $this->configure();

        // Injects configuration in the sender
        $sender
            ->setFetchMode($this->fetchMode)
            ->setIdentifier($this->identifier)
            ->setFilters($this->filters)
            ->setEntityFqcn($options['entity'])
        ;

        return $sender;
    }

    /**
     * This method is responsible of defining is the configuration was explicitly given, or if it should be guessed.
     *
     * @return void
     */
    protected function configure(): void
    {
        // If a selector was defined, no need to check entity's identifiers mapping
        if (!empty($this->options['selector'])) {
            $this->configureSelector();

            return;   
        }

        // No selector was defined, we can try and guess how the entity works.
        $this->guessConfiguration();
    }

    /**
     * This method defines how to build a filter for the entity when the user provided an explicit configuration.
     *
     * @return void
     */
    protected function configureSelector(): void
    {
        $selector = $this->options['selector'];

        if (\is_string($selector)) {
            $selector = (array) $selector;
        }

        foreach ($selector as $key => $value) {
            if (\is_int($key)) {
                // Key is integer, getter matching the property is considered "natural".
                $this->assertPropertyIsReadable($value);

                $this->filters[$value] = $value;
            } else {
                // otherwise, the getter is "unnatural" and is explicitely defined by the user. The key is the property, the value is the getter.
                
                $this->assertPropertyIsReadable($value);
                $this->filters[$key] = $value;
            }
        }

        $this->fetchMode  = DoctrineSender::SELECTION_MODE_FILTER;
    }

    /**
     * This method guesses how to build a filter for the entity when the user didn't provide an explicit configuration.
     * @return [type] [description]
     */
    protected function guessConfiguration(): void
    {
        $this->logger->info('No explicit configuration for DoctrineTransport, will try to guess how the entity works.');

        $metadata    = $this->entityManager->getClassMetadata($this->options['entity']);
        $identifiers = $metadata->getIdentifierFieldNames();

        // No identifier was defined, we have no way of selecting the entity → stop here.
        if (0 === \count($identifiers)) {
            $message = sprintf('Class %s does not define a unique identifier and you did not define any `selector` option. You need to define either so that the transport can try and fetch the entity prior to persisting it.', $this->options['entity']);
            
            $this->logger->error($message);

            throw new InvalidIdentifiersCountException($message);
        }

        // Composite identifier: ask user to fallback to selector mode so we have less code to maintain.
        if (\count($identifiers) > 1) {
            $message = sprintf('Composite identifiers are not supported (%s). Please use multiple selector.', implode(',', $identifiers));
            
            $this->logger->error($message);

            throw new InvalidIdentifiersCountException($message);
        }

        $identifier = $identifiers[0];

        $this->logger->info('Found unique identifier: {identifier}', [
            'identifier' => $identifier,
        ]);

        // Below this point we have one single identifier we can use. Last check:: can we read the identifier's value?
        $this->assertPropertyIsReadable($identifier);

        $this->logger->info('{identifier} is readable!', [
            'identifier' => $identifier,
        ]);

        $this->identifier = $identifier;
        $this->fetchMode  = DoctrineSender::SELECTION_MODE_IDENTIFER;
    }

    /**
     * Ensures the given property is readable on the subject entity.
     *
     * @param  string $property
     *
     * @throws UnreadableEntityPropertyException The given property isn't readable by the PropertyAccessor
     *
     * @return void
     */
    protected function assertPropertyIsReadable(string $property): void
    {
        $reflection     = new ReflectionClass($this->options['entity']);
        $entityInstance = $reflection->newInstanceWithoutConstructor();

        if (!$this->accessor->isReadable($entityInstance, $property)) {
            $message = sprintf('Cound not define a way to access property (%s) value in %s. Did you define a public getter?', $property, $this->options['entity']);

            throw new UnreadableEntityPropertyException($message);
        }
    }
}
