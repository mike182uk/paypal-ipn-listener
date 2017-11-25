<?php

namespace ListenerBuilder\Guzzle;

use Mdb\PayPal\Ipn\ListenerBuilder\Guzzle\ArrayListenerBuilder as BaseListenerBuilder;

class ArrayListenerBuilder extends BaseListenerBuilder
{
    /**
     * @var string
     */
    private $serviceEndpoint;

    /**
     * @param string $serviceEndpoint
     */
    public function setServiceEndpoint($serviceEndpoint)
    {
        $this->serviceEndpoint = $serviceEndpoint;
    }

    /**
     * @return string
     */
    protected function getServiceEndpoint()
    {
        return $this->serviceEndpoint;
    }
}
