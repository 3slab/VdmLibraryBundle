<?php

namespace App\Command;

use App\Model\Metadata;
use App\Model\Trace;
use App\Model\Message;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBus;
use Symfony\Component\Messenger\MessageBusInterface;

class SendCommand extends Command
{
    protected static $defaultName = 'app:send';

    /**
     * @var MessageBus
     */
    private $bus;

    public function __construct(string $name = null, MessageBusInterface $bus)
    {
        parent::__construct($name);
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
