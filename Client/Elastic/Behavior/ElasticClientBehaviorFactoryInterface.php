<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Client\Elastic\Behavior;

use Psr\Log\LoggerInterface;
use Vdm\Bundle\LibraryBundle\Client\Elastic\ElasticClientInterface;

interface ElasticClientBehaviorFactoryInterface
{
    public static function priority(int $priority = 0);

    public function createDecoratedElasticClient(LoggerInterface $logger, ElasticClientInterface $elasticClient, array $options);

    public function support(array $options);
}
