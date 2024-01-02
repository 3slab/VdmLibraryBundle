<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Command;

use Vdm\Bundle\LibraryBundle\Model\Metadata;
use Vdm\Bundle\LibraryBundle\Model\Trace;
use Vdm\Bundle\LibraryBundle\Model\Message;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class SendCommand extends Command
{
    protected static $defaultName = 'app:send';

    /**
     * @var MessageBusInterface $bus
     */
    private $bus;

    public function __construct(MessageBusInterface $bus)
    {
        parent::__construct();
        $this->bus = $bus;
    }

    protected function configure()
    {
        // ...
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $random = rand(1, 1000);

        $trace = new Trace('itv', 'enter');
        $metadata = new Metadata('vdm_category', 'toto');

        $message = new Message($random);
        $message->setMetadatas([$metadata]);
        $message->setTraces([$trace]);

        $this->bus->dispatch($message);

        return 0;
    }
}
