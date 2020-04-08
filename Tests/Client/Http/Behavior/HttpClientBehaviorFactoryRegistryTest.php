<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Tests\Client\Http\Behavior;

use PHPUnit\Framework\TestCase;
use Vdm\Bundle\LibraryBundle\Client\Http\Behavior\HttpClientBehaviorFactoryRegistry;
use Vdm\Bundle\LibraryBundle\Client\Http\Behavior\RetryHttpClientBehaviorFactory;
use Vdm\Bundle\LibraryBundle\Client\Http\Behavior\MonitoringHttpClientBehaviorFactory;
use Symfony\Component\HttpClient\MockHttpClient;

class HttpClientBehaviorFactoryRegistryTest extends TestCase
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
     * @var HttpClient $httpClient
     */
    private $httpClient;

    /**
     * @var HttpClientBehaviorFactoryRegistry $httpClientBehavior
     */
    private $httpClientBehavior;

    protected function setUp(): void
    {
        $this->logger = $this->getMockBuilder(\Psr\Log\LoggerInterface::class)->getMock();
        $this->eventDispatcher = $this->getMockBuilder(\Psr\EventDispatcher\EventDispatcherInterface::class)->getMock();
        $this->httpClient = new MockHttpClient();

        $this->httpClientBehavior = new HttpClientBehaviorFactoryRegistry($this->logger);
    }

    public function testAddFactory()
    {
        $retryHttpClientBehaviorFactory = new RetryHttpClientBehaviorFactory();
        $monitoringrHttpClientBehaviorFactory = new MonitoringHttpClientBehaviorFactory($this->eventDispatcher);
        $priorityRetry = 100;
        $priorityMonitoring = 0;

        $property = new \ReflectionProperty(\Vdm\Bundle\LibraryBundle\Client\Http\Behavior\HttpClientBehaviorFactoryRegistry::class, 'httpClientBehavior');
        $property->setAccessible(true);
        $value = $property->getValue($this->httpClientBehavior);
        $this->assertEmpty($value);
        try {
            $this->httpClientBehavior->addFactory($retryHttpClientBehaviorFactory, $priorityRetry);
            $this->httpClientBehavior->addFactory($monitoringrHttpClientBehaviorFactory, $priorityMonitoring);
        } catch (\Exception $exception) {

        }

        $value = $property->getValue($this->httpClientBehavior);
        $this->assertNotEmpty($value);
        $this->assertCount(2, $value);
    }

    public function testCreate()
    {
        $httpClient = $this->httpClientBehavior->create($this->httpClient, []);

        $this->assertInstanceOf(\Symfony\Contracts\HttpClient\HttpClientInterface::class, $httpClient);
    }


    public function testCreateNotSupport()
    {
        $httpClient = $this->httpClientBehavior->create($this->httpClient, []);

        $this->assertInstanceOf(\Symfony\Contracts\HttpClient\HttpClientInterface::class, $httpClient);
    }

    public function testCreateSupport()
    {
        $monitoringrHttpClientBehaviorFactory = new MonitoringHttpClientBehaviorFactory($this->eventDispatcher);
        $priorityMonitoring = 0;
        $this->httpClientBehavior->addFactory($monitoringrHttpClientBehaviorFactory, $priorityMonitoring);
        $httpClient = $this->httpClientBehavior->create($this->httpClient, ['monitoring' => ['enabled' => true]]);

        $this->assertInstanceOf(\Vdm\Bundle\LibraryBundle\Client\Http\MonitoringHttpClientBehavior::class, $httpClient);
    }
}
