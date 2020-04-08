<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Executor\Ftp;

use Symfony\Component\Messenger\Envelope;
use Vdm\Bundle\LibraryBundle\Model\Message;
use Vdm\Bundle\LibraryBundle\Stamp\StopAfterHandleStamp;

class DefaultFtpExecutor extends AbstractFtpExecutor
{
    public function execute(array $files): iterable
    {
        foreach ($files as $key => $file) {
            if (isset($file['type']) && $file['type'] === 'file') {
                $file = $this->ftpClient->get($file);
                $message = new Message($file);
                // Put the stop stamp on the last file
                if (array_key_last($files) === $key) {
                    yield new Envelope($message, [new StopAfterHandleStamp()]);
                } else {
                    yield new Envelope($message);
                }
            }
        }

        yield new Envelope(new Message(""), [new StopAfterHandleStamp()]);
    }
}
