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
        $files = array_filter($files, function($file) {
            return (isset($file['type']) && $file['type'] === 'file');
        });
        
        foreach ($files as $key => $file) {
            $file = $this->ftpClient->get($file);
            $message = new Message($file);

            yield $this->getEnvelope($files, $key, $message);
        }

        yield new Envelope(new Message(""), [new StopAfterHandleStamp()]);
    }
    
    private function getEnvelope(array $files, int $key, Message $message): Envelope
    {
        $stamps = [];

        // Put the stop stamp on the last file
        if (array_key_last($files) === $key) {
            $stamps = [new StopAfterHandleStamp()];
        }

        return new Envelope($message, $stamps);
    }
}
