<?php

namespace Vdm\Bundle\LibraryBundle\Executor\Ftp;

use Symfony\Component\Messenger\Envelope;
use Vdm\Bundle\LibraryBundle\Model\Message;

class DefaultFtpExecutor extends AbstractFtpExecutor
{
    public function execute(array $files): iterable
    {
        foreach ($files as $file) {
            if (isset($file['type']) && $file['type'] === 'file') {
                $file = $this->ftpClient->get($file);
                $message = new Message($file);
                yield new Envelope($message);
            }
        }
    }
}
