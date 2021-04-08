<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Command;

use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\RoutableMessageBus;
use Vdm\Bundle\LibraryBundle\Service\CollectWorker;
use Vdm\Bundle\LibraryBundle\Transport\TransportCollectableInterface;

/**
 * Class ConsumeMessagesCommand
 * @package Vdm\Bundle\LibraryBundle\Command
 */
class CollectMessagesCommand extends Command
{
    protected static $defaultName = 'vdm:collect';

    private $routableBus;
    private $collectorLocator;
    private $logger;
    private $collectorNames;
    private $eventDispatcher;

    public function __construct(
        RoutableMessageBus $routableBus,
        ContainerInterface $collectorLocator,
        EventDispatcherInterface $eventDispatcher,
        LoggerInterface $logger = null,
        array $collectorNames = []
    ) {
        $this->routableBus = $routableBus;
        $this->collectorLocator = $collectorLocator;
        $this->logger = $logger;
        $this->collectorNames = $collectorNames;
        $this->eventDispatcher = $eventDispatcher;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $defaultcollectorName = 1 === \count($this->collectorNames) ? current($this->collectorNames) : null;

        $this
            ->setDefinition([
                new InputArgument(
                    'collectors',
                    InputArgument::IS_ARRAY,
                    'Names of the collectors/transports to consume in order of priority', $defaultcollectorName ? [$defaultcollectorName] : []
                ),
                new InputOption(
                    'bus',
                    'b',
                    InputOption::VALUE_REQUIRED,
                    'Name of the bus to which received messages should be dispatched (if not passed, bus is determined automatically)'
                ),
            ])
            ->setDescription('Collect messages')
            ->setHelp(<<<'EOF'
The <info>%command.name%</info> command collects messages and dispatches them to the message bus.

    <info>php %command.full_name% <collector-name></info>

To receive from multiple transports, pass each name:

    <info>php %command.full_name% collector1 collector2</info>

Use the --bus option to specify the message bus to dispatch received messages
to instead of trying to determine it automatically. This is required if the
messages didn't originate from Messenger or from a VDM collect transport:

    <info>php %command.full_name% <collector-name> --bus=event_bus</info>
EOF
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output instanceof ConsoleOutputInterface ? $output->getErrorOutput() : $output);

        if ($this->collectorNames && 0 === \count($input->getArgument('collectors'))) {
            $io->block('Which transports/collectors do you want to use?', null, 'fg=white;bg=blue', ' ', true);

            $io->writeln('Choose which collectors you want to collect messages from in order of priority.');
            if (\count($this->collectorNames) > 1) {
                $io->writeln(sprintf('Hint: to collect from multiple, use a list of their names, e.g. <comment>%s</comment>', implode(', ', $this->collectorNames)));
            }

            $question = new ChoiceQuestion('Select collectors to collect:', $this->collectorNames, 0);
            $question->setMultiselect(true);

            $input->setArgument('collectors', $io->askQuestion($question));
        }

        if (0 === \count($input->getArgument('collectors'))) {
            throw new RuntimeException('Please pass at least one collector.');
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $collectors = [];
        foreach ($collectorNames = $input->getArgument('collectors') as $collectorName) {
            if (!$this->collectorLocator->has($collectorName)) {
                $message = sprintf('The collector "%s" does not exist.', $collectorName);
                if ($this->collectorNames) {
                    $message .= sprintf(' Valid collectors are: %s.', implode(', ', $this->collectorNames));
                }

                throw new RuntimeException($message);
            }

            $collector = $this->collectorLocator->get($collectorName);
            if (!$collector instanceof TransportCollectableInterface) {
                $message = sprintf('The collector "%s" is not collectable. Use vdm:consume instead', $collectorName);
                throw new RuntimeException($message);
            }

            $collectors[$collectorName] = $collector;
        }

        $io = new SymfonyStyle($input, $output instanceof ConsoleOutputInterface ? $output->getErrorOutput() : $output);
        $io->success(sprintf('Collecting messages from transport%s "%s".', \count($collectors) > 0 ? 's' : '', implode(', ', $collectorNames)));

        if (OutputInterface::VERBOSITY_VERBOSE > $output->getVerbosity()) {
            $io->comment('Re-run the command with a -vv option to see logs about collected messages.');
        }

        $bus = $input->getOption('bus') ? $this->routableBus->getMessageBus($input->getOption('bus')) : $this->routableBus;

        $worker = new CollectWorker($collectors, $bus, $this->eventDispatcher, $this->logger);
        $worker->run();

        return 0;
    }
}