<?php

namespace Vdm\Bundle\LibraryBundle\FtpClient;

use League\Flysystem\Filesystem;

interface FtpClientInterface
{
    public function get(string $dirpath): ?array;
    
    public function getFileSystem(): Filesystem;
}
