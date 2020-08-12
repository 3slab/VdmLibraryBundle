<?php


namespace Vdm\Bundle\LibraryBundle\Monitoring;


use Psr\Log\LoggerInterface;

class StatsStorageFactory
{
    protected $statsStorageClasses = [];

    /**
     * @var LoggerInterface $logger
     */
    protected $logger;

    /**
     * NullStatsStorage constructor.
     * @param LoggerInterface $messengerLogger
     */
    public function __construct(LoggerInterface $messengerLogger)
    {
        $this->logger = $messengerLogger;
    }

    public function registerStatStorageClass($class)
    {
        $this->statsStorageClasses[$class::getType()] = $class;
    }

    public function build($type, $options, $appName)
    {
        if (!array_key_exists($type, $this->statsStorageClasses)) {
            $this->logger->warning("stats storage $type does not exist. Fallback to null storage");
            $type = 'null';
        }

        return new $this->statsStorageClasses[$type]($options, $appName, $this->logger);
    }
}
