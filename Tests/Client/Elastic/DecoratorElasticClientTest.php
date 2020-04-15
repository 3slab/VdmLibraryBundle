<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Tests\Client\Elastic;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Vdm\Bundle\LibraryBundle\Client\Elastic\DecoratorElasticClient;
use Vdm\Bundle\LibraryBundle\Client\Elastic\ElasticClient;
use Vdm\Bundle\LibraryBundle\Model\Message;

class DecoratorElasticClientTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject $logger
     */
    private $logger;

    /**
     * @var ElasticClient $elasticClient
     */
    private $elasticClient;

    /**
     * @var DecoratorElasticClient $decoratorElasticClient
     */
    private $decoratorElasticClient;

    protected function setUp(): void
    {
        $this->logger = $this->getMockBuilder(\Psr\Log\LoggerInterface::class)->getMock();
        $this->client = $this->getMockBuilder(\Elasticsearch\Client::class)->disableOriginalConstructor()->setMethods(['index'])->getMock();
        $this->elasticClient = $this->getMockBuilder(ElasticClient::class)->disableOriginalConstructor()->setMethods(['post'])->getMock();
        $this->elasticClient->setClient($this->client);
        $this->decoratorElasticClient = $this->getMockForAbstractClass(DecoratorElasticClient::class, [$this->logger, $this->elasticClient]);
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
        $reponsePost = $this->decoratorElasticClient->post($envelope, $index);

        $this->assertSame($reponseClient, $reponsePost);
    }
}
