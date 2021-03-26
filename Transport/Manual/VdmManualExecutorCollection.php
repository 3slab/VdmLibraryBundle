<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle /blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Transport\Manual;

use Symfony\Component\DependencyInjection\ServiceLocator;

class VdmManualExecutorCollection
{
    /**
     * @var ServiceLocator
     */
    protected $locator;

    /**
     * VdmManualExecutorCollection constructor.
     * @param ServiceLocator $locator
     */
    public function __construct(ServiceLocator $locator)
    {
        $this->locator = $locator;
    }

    /**
     * @param string $executorId
     * @return VdmManualExecutorInterface
     */
    public function get(string $executorId): VdmManualExecutorInterface
    {
        return $this->locator->get($executorId);
    }

    /**
     * @param string $executorId
     * @return bool
     */
    public function has(string $executorId): bool
    {
        return $this->locator->has($executorId);
    }
}