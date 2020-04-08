<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Tests\Client\Elastic;

use PHPUnit\Framework\TestCase;
use Vdm\Bundle\LibraryBundle\Client\Elastic\ElasticClientFactory;

class ElasticClientFactoryTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject $logger
     */
    private $logger;

    /**
     * @var ElasticClientFactory $elasticClientFactory
     */
    private $elasticClientFactory;

    protected function setUp(): void
    {
        $this->logger = $this->getMockBuilder(\Psr\Log\LoggerInterface::class)->getMock();
        $this->elasticClientFactory = new ElasticClientFactory($this->logger);
    }

    public function testCreate()
    {    
        $elasticClient = $this->elasticClientFactory->create("elasticsearch://elasticsearch:9200", []);

        $this->assertInstanceOf(\Vdm\Bundle\LibraryBundle\Client\Elastic\ElasticClient::class, $elasticClient);
    }
}
