<?php

namespace Vdm\Bundle\LibraryBundle\FtpClient;

use League\Flysystem\Filesystem;

interface FtpClientInterface
{
    public function get(string $dsn, array $options): ?array;
    
    public function getFileSystem(): Filesystem;
}
