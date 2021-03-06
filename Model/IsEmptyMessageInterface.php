<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Model;

use Symfony\Component\Serializer\Annotation\Ignore;

/**
 * interface IsEmptyMessageInterface
 *
 * @package Vdm\Bundle\LibraryBundle\Model
 */
interface IsEmptyMessageInterface
{
    /**
     * @Ignore()
     * @return bool
     */
    public function isEmpty(): bool;
}
