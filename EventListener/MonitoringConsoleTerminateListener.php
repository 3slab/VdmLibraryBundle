<?php

namespace Vdm\Bundle\LibraryBundle\EventListener;

use Vdm\Bundle\LibraryBundle\Monitoring\Model\ErrorStateStat;
use Vdm\Bundle\LibraryBundle\Monitoring\Model\RunningStat;
use Vdm\Bundle\LibraryBundle\Monitoring\StatsStorageInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class MonitoringConsoleTerminateListener implements EventSubscriberInterface
{
    /**
     * @var StatsStorageInterface
     */
    private $storage;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * MonitoringConsoleTerminateListener constructor.
     *
     * @param StatsStorageInterface $storage
     * @param LoggerInterface|null $messengerLogger
     */
    public function __construct(StatsStorageInterface $storage, LoggerInterface $messengerLogger = null)
    {
        $this->storage = $storage;
        $this->logger = $messengerLogger;
    }

    /**
     * Method executed on ConsoleTerminateEvent event
     *
     * @param ConsoleTerminateEvent $event
     */
    public function onConsoleTerminate(ConsoleTerminateEvent $event)
    {
        // Just to be sure sends again the stopped stats
        $runningStat = new RunningStat(false);
        $this->storage->sendRunningStat($runningStat);

        if (null !== $this->logger) {
            $this->logger->info('ConsoleTerminateEvent - Running stats sent - {isRunning}',
                [
                    'isRunning' => $runningStat->isRunning() ?: '0'
                ]
            );
        }

        // Send the exit code
        if ($event->getExitCode() !== 0) {
            $errorStateStat = new ErrorStateStat($event->getExitCode());
            $this->storage->sendErrorStateStat($errorStateStat);

            if (null !== $this->logger) {
                $this->logger->info('ConsoleTerminateEvent - Error state stats sent with code {code}',
                    [
                        'code' => $errorStateStat->getCode()
                    ]
                );
            }
        }

        // Flush stats for storage that needs it
        $this->storage->flush();

        if (null !== $this->logger) {
            $this->logger->info('ConsoleTerminateEvent - Stats storage flushed');
        }
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            ConsoleTerminateEvent::class => 'onConsoleTerminate',
        ];
    }
}
