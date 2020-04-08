<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Tests\Executor\Http;

use PHPUnit\Framework\TestCase;

class AbstractHttpExecutorTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject $logger
     */
    private $logger;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject $serializer
     */
    private $serializer;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject $httpClient
     */
    private $httpClient;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject $httpExecutor
     */
    private $httpExecutor;

    protected function setUp(): void
    {
        $this->logger = $this->getMockBuilder(\Psr\Log\LoggerInterface::class)->getMock();
        $this->serializer = $this->getMockBuilder(\Symfony\Component\Messenger\Transport\Serialization\SerializerInterface::class)->getMock();
        $this->httpClient = $this->getMockBuilder(\Symfony\Contracts\HttpClient\HttpClientInterface::class)->getMock();
        $this->httpExecutor = $this->getMockForAbstractClass(
            \Vdm\Bundle\LibraryBundle\Executor\Http\DefaultHttpExecutor::class, 
            [$this->logger, $this->serializer, $this->httpClient]
        );
    }

    public function testExecute()
    {
        $dsn = "https://ipconfig.io/json";
        $method = "GET";
        $options = [];

        $iterator = $this->httpExecutor->execute($dsn, $method, $options);
        $stamps = $iterator->current()->all();

        $this->assertInstanceOf(\Symfony\Component\Messenger\Envelope::class, $iterator->current());
        $this->assertArrayHasKey(\Vdm\Bundle\LibraryBundle\Stamp\StopAfterHandleStamp::class, $stamps);
    }
}
