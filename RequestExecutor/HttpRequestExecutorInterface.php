<?php

namespace Vdm\Bundle\LibraryBundle\RequestExecutor;

interface HttpRequestExecutorInterface
{
    public function execute(string $dsn, string $method, array $options): string;
}
