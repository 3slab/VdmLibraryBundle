<?php

namespace Vdm\Bundle\LibraryBundle\Client\Ftp;

use League\Flysystem\Filesystem;

interface FtpClientInterface
{
    public function get(string $filename): ?string;

    public function list(string $dirpath): ?array;
    
    public function getFileSystem(): Filesystem;
}
