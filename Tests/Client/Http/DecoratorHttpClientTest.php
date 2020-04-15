<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Tests\Client\Http;

use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Symfony\Contracts\HttpClient\ResponseStreamInterface;
use Vdm\Bundle\LibraryBundle\Client\Http\DecoratorHttpClient;

class DecoratorHttpClientTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject $logger
     */
    private $logger;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject $httpClient
     */
    private $httpClient;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject $eventDispatcher
     */
    private $eventDispatcher;

    /**
     * @var DecoratorHttpClient $decoratorHttpClient
     */
    private $decoratorHttpClient;

    protected function setUp(): void
    {
        $this->logger = $this->getMockBuilder(\Psr\Log\LoggerInterface::class)->getMock();
        $this->httpClient = $this->getMockBuilder(\Symfony\Contracts\HttpClient\HttpClientInterface::class)->getMock();
        $this->eventDispatcher = $this->getMockBuilder(\Psr\EventDispatcher\EventDispatcherInterface::class)->getMock();

        $this->decoratorHttpClient = $this->getMockForAbstractClass(
            DecoratorHttpClient::class, 
            [$this->logger, $this->httpClient, $this->eventDispatcher]
        );
    }

    public function testRequest()
    {
        $response = $this->decoratorHttpClient->request("GET", "https://ipconfig.io/json", []);

        $this->assertInstanceOf(ResponseInterface::class, $response);
    }

    public function testStream()
    {
        $response = $this->getMockBuilder(ResponseInterface::class)->getMock();
        $responseStream = $this->decoratorHttpClient->stream($response);

        $this->assertInstanceOf(ResponseStreamInterface::class, $responseStream);
    }
}
