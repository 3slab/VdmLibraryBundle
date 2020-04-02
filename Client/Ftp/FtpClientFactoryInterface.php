<?php

namespace Vdm\Bundle\LibraryBundle\Client\Ftp;

interface FtpClientFactoryInterface
{
    public function create(string $dsn, array $options): FtpClient;
}
