<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Client\Ftp\Event;

use Symfony\Contracts\EventDispatcher\Event;

class FtpClientReceivedEvent extends Event
{
    /**
     * @var array|null $file
     */
    private $file;

    /**
     * FtpClientReceivedEvent constructor
     */
    public function __construct(?array $file)
    {
        $this->file = $file;
    }

    /**
     * @return array|null
     */
    public function getFile(): ?array
    {
        return $this->file;
    }
}
