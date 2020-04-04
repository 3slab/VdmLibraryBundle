<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Client\Elastic\Behavior;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Vdm\Bundle\LibraryBundle\Client\Elastic\ElasticClientInterface;
use Vdm\Bundle\LibraryBundle\Client\Elastic\MonitoringElasticClientBehavior;

class MonitoringElasticClientBehaviorFactory implements ElasticClientBehaviorFactoryInterface
{
    private $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public static function priority(int $priority = -100)
    {
        return $priority;
    }

    public function createDecoratedElasticClient(LoggerInterface $logger, ElasticClientInterface $elasticClient, array $options)
    {
        return new MonitoringElasticClientBehavior($logger, $elasticClient, $this->eventDispatcher);
    }

    public function support(array $options)
    {
        if (isset($options['monitoring']['enabled']) && $options['monitoring']['enabled'] === true) {
            return true;
        }

        return false;
    }
}
