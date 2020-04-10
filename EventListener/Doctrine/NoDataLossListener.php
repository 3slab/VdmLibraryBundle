<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\EventListener\Doctrine;

use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\UnitOfWork;
use Symfony\Component\PropertyAccess\PropertyAccess;

class NoDataLossListener implements EventSubscriber
{
    /**
     * @var UnitOfWork
     */
    protected $uow;

    /**
     * @var array
     */
    protected $whiteListing;

    public function __construct(EntityManagerInterface $em, array $whiteListing = [])
    {
        $this->uow          = $em->getUnitOfWork();
        $this->whiteListing = $whiteListing;
    }

    /**
     *  {inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return [
            'preUpdate',
        ];
    }

    /**
     * Before updating an entity, make sur there is no loss of data (i.e overwriting !null with null)
     *
     * @param  LifecycleEventArgs $args
     *
     * @return void
     */
    public function preUpdate(LifecycleEventArgs $args): void
    {
        $accessor = PropertyAccess::createPropertyAccessor();
        $entity   = $args->getEntity();

        // Diffs old and new version of the entity
        $this->uow->computeChangeSets();

        $changeSet = $this->uow->getEntityChangeSet($entity);

        $nullableFields = $this->getNullableFields($entity);

        $illegalChanges = array_filter($changeSet, function(array $diff, string $property) use ($nullableFields) {
            // diff is an array with two indexes: 0 = old value, 1 = new value
            return !in_array($property, $nullableFields) && is_null($diff[1]);
        }, ARRAY_FILTER_USE_BOTH);

        // Revert to original value for illegal changes
        foreach ($illegalChanges as $property => $diff) {
            $accessor->setValue($entity, $property, $diff[0]);
        }
    }

    /**
     * Defines nullable fields for entity being updated.
     *
     * @param  object $entity
     *
     * @return array
     */
    protected function getNullableFields(object $entity): array
    {
        if (empty($this->whiteListing[get_class($entity)])) {
            return [];
        }

        return $this->whiteListing[get_class($entity)];        
    }
}
