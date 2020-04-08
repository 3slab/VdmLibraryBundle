<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Tests\Client\Elastic\Behavior;

use PHPUnit\Framework\TestCase;
use Vdm\Bundle\LibraryBundle\Client\Elastic\Behavior\ElasticClientBehaviorFactoryRegistry;
use Vdm\Bundle\LibraryBundle\Client\Elastic\Behavior\RetryElasticClientBehaviorFactory;
use Vdm\Bundle\LibraryBundle\Client\Elastic\Behavior\MonitoringElasticClientBehaviorFactory;
use Vdm\Bundle\LibraryBundle\Client\Elastic\ElasticClient;

class ElasticClientBehaviorFactoryRegistryTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject $logger
     */
    private $logger;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject $eventDispatcher
     */
    private $eventDispatcher;

    /**
     * @var ElasticClient $elasticClient
     */
    private $elasticClient;

    /**
     * @var ElasticClientBehaviorFactoryRegistry $elasticClientBehavior
     */
    private $elasticClientBehavior;

    protected function setUp(): void
    {
        $this->logger = $this->getMockBuilder(\Psr\Log\LoggerInterface::class)->getMock();
        $this->eventDispatcher = $this->getMockBuilder(\Psr\EventDispatcher\EventDispatcherInterface::class)->getMock();
        $this->elasticClient = new ElasticClient('elasticsearch', 9200, '', '', 'http', $this->logger);

        $this->elasticClientBehavior = new ElasticClientBehaviorFactoryRegistry($this->logger);
    }

    public function testAddFactory()
    {
        $retryElasticClientBehaviorFactory = new RetryElasticClientBehaviorFactory();
        $monitoringrElasticClientBehaviorFactory = new MonitoringElasticClientBehaviorFactory($this->eventDispatcher);
        $priorityRetry = 100;
        $priorityMonitoring = 0;

        $property = new \ReflectionProperty(\Vdm\Bundle\LibraryBundle\Client\Elastic\Behavior\ElasticClientBehaviorFactoryRegistry::class, 'elasticClientBehavior');
        $property->setAccessible(true);
        $value = $property->getValue($this->elasticClientBehavior);
        $this->assertEmpty($value);
        try {
            $this->elasticClientBehavior->addFactory($retryElasticClientBehaviorFactory, $priorityRetry);
            $this->elasticClientBehavior->addFactory($monitoringrElasticClientBehaviorFactory, $priorityMonitoring);
        } catch (\Exception $exception) {

        }

        $value = $property->getValue($this->elasticClientBehavior);
        $this->assertNotEmpty($value);
        $this->assertCount(2, $value);
    }

    public function testCreateNotSupport()
    {
        $elasticClient = $this->elasticClientBehavior->create($this->elasticClient, ['monitoring' => ['enabled' => true]]);

        $this->assertInstanceOf(ElasticClient::class, $elasticClient);
    }

    public function testCreateSupport()
    {
        $monitoringrElasticClientBehaviorFactory = new MonitoringElasticClientBehaviorFactory($this->eventDispatcher);
        $priorityMonitoring = 0;
        $this->elasticClientBehavior->addFactory($monitoringrElasticClientBehaviorFactory, $priorityMonitoring);
        $elasticClient = $this->elasticClientBehavior->create($this->elasticClient, ['monitoring' => ['enabled' => true]]);

        $this->assertInstanceOf(\Vdm\Bundle\LibraryBundle\Client\Elastic\MonitoringElasticClientBehavior::class, $elasticClient);
    }
}
