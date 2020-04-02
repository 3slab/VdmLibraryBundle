<?php

namespace Vdm\Bundle\LibraryBundle\Executor\Ftp;

use Vdm\Bundle\LibraryBundle\Model\Message;

interface FtpExecutorInterface
{
    public function execute(array $file): Message;
}
