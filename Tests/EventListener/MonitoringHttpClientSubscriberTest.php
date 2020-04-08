<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Tests\EventListener;

use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Symfony\Component\Messenger\Envelope;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Vdm\Bundle\LibraryBundle\Client\Http\Event\HttpClientReceivedResponseEvent;
use Vdm\Bundle\LibraryBundle\EventListener\MonitoringHttpClientSubscriber;
use Vdm\Bundle\LibraryBundle\Monitoring\StatsStorageInterface;

class MonitoringHttpClientSubscriberTest extends TestCase
{
    /**
     * @var StatsStorageInterface $statsStorageInterface
     */
    private $statsStorageInterface;

    protected function getSubscriber(array $calls)
    {
        $this->statsStorageInterface = $this->getMockBuilder(StatsStorageInterface::class)->getMock();
        $this->responseInterface = $this->getMockBuilder(ResponseInterface::class)->getMock();
        $this->statsStorageInterface->expects($calls['sendHttpResponseStat'])->method('sendHttpResponseStat');

        return new MonitoringHttpClientSubscriber($this->statsStorageInterface, new NullLogger());
    }

    /**
     * @dataProvider dataProviderTestOnHttpClientReceivedResponseEvent
     */
    public function testOnHttpClientReceivedResponseEvent($methodCall)
    {
        $listener = $this->getSubscriber(['sendHttpResponseStat' => $methodCall]);

        $event = new HttpClientReceivedResponseEvent($this->responseInterface);

        $listener->onHttpClientReceivedResponseEvent($event);
    }

    public function dataProviderTestOnHttpClientReceivedResponseEvent()
    {
        yield [
            $this->once()
        ];
    }
}
