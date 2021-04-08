<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Command;

use Symfony\Component\Messenger\Command\ConsumeMessagesCommand as SymfonyConsumeMessagesCommand;

/**
 * Class ConsumeMessagesCommand
 * @package Vdm\Bundle\LibraryBundle\Command
 */
class ConsumeMessagesCommand extends SymfonyConsumeMessagesCommand
{
    protected static $defaultName = 'vdm:consume';
}
