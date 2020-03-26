<?php

namespace Vdm\Bundle\LibraryBundle\FileExecutor;

use Vdm\Bundle\LibraryBundle\Model\Message;

interface FtpFileExecutorInterface
{
    public function execute(array $file): Message;
}
