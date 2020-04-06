<?php

namespace ListenerBuilder\Guzzle;

use Mdb\PayPal\Ipn\ListenerBuilder\Guzzle\ArrayListenerBuilder as BaseListenerBuilder;

class ArrayListenerBuilder extends BaseListenerBuilder
{
    /**
     * @var string
     */
    private $serviceEndpoint;

    public function setServiceEndpoint(string $serviceEndpoint)
    {
        $this->serviceEndpoint = $serviceEndpoint;
    }

    protected function getServiceEndpoint() : string
    {
        return $this->serviceEndpoint;
    }
}
