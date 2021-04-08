<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Service\Monitoring;

/**
 * Class Monitoring
 * @package Vdm\Bundle\LibraryBundle\Service\Monitoring
 */
class Monitoring
{
    public const RUNNING_STAT = 'vdm.running';

    public const PRODUCED_STAT = 'vdm.produced';

    public const ERROR_STAT = 'vdm.error';

    public const CONSUMED_STAT = 'vdm.consumed';

    public const HANDLED_STAT = 'vdm.handled';

    public const MEMORY_STAT = 'vdm.memory';

    public const DURATION_STAT = 'vdm.duration';
}
