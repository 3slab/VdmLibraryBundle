<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Tests\Client\Elastic;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Vdm\Bundle\LibraryBundle\Client\Elastic\ElasticClient;
use Vdm\Bundle\LibraryBundle\Model\Message;

class ElasticClientTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject $logger
     */
    private $logger;

    /**
     * @var ElasticClient $elasticClient
     */
    private $elasticClient;

    protected function setUp(): void
    {
        $this->logger = $this->getMockBuilder(\Psr\Log\LoggerInterface::class)->getMock();
        $this->elasticClient = $this->getMockBuilder(ElasticClient::class)->disableOriginalConstructor()->setMethods(['post'])->getMock();
        $this->client = $this->getMockBuilder(\Elasticsearch\Client::class)->disableOriginalConstructor()->setMethods(['index'])->getMock();
        $this->elasticClient->setClient($this->client);
    }

    public function testPost()
    {
        $index = 'test';
        $body = 'test';

        $params = [
            'index' => $index,
            'body'  => (is_array($body)) ? $body : [ 'message' => $body ]
        ];
        $reponseClient = $this->client->index($params);

        $envelope = new Envelope(new Message($body));
        $reponsePost = $this->elasticClient->post($envelope, $index);

        $this->assertSame($reponseClient, $reponsePost);
    }

    public function testGetClient()
    {     
        $client = $this->elasticClient->getClient();

        $this->assertInstanceOf(\Elasticsearch\Client::class, $client);
    }
}
