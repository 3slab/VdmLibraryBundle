<?php

namespace Vdm\Bundle\LibraryBundle\FtpClient;

interface FtpClientFactoryInterface
{
    public function create(string $dsn, array $options): FtpClient;
}
