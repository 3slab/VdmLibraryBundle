<?php

namespace Vdm\Bundle\LibraryBundle\Transport;

use Symfony\Component\Messenger\Transport\TransportInterface;

abstract class HttpTransportConfiguration implements TransportInterface
{
    /** 
     * @var TransportInterface $httpTransportConfiguration
    */
    protected $httpTransportConfiguration;

    public function __construct(TransportInterface $transportInterface) {
        $this->transportInterface = $transportInterface;
    }
}
