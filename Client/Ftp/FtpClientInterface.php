<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Client\Ftp;

use League\Flysystem\FilesystemInterface;

interface FtpClientInterface
{
    public function get(array $file): array;

    public function list(string $dirpath): ?array;
    
    public function getFileSystem(): FilesystemInterface;
}
