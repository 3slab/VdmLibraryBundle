<?php

namespace Vdm\Bundle\LibraryBundle\Client\Ftp;

use League\Flysystem\Filesystem;

interface FtpClientInterface
{
    public function get(string $dirpath, array $file): array;

    public function list(string $dirpath): ?array;
    
    public function getFileSystem(): Filesystem;
}
