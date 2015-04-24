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
     * @param string $serviceEnpoint
     */
    public function setServiceEndpoint($serviceEnpoint)
    {
        $this->serviceEndpoint = $serviceEnpoint;
    }

    /**
     * @return string
     */
    protected function getServiceEndpoint()
    {
        return $this->serviceEndpoint;
    }
}
