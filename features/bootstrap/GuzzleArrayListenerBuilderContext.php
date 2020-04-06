<?php

use ListenerBuilder\Guzzle\ArrayListenerBuilder;
use Mdb\PayPal\Ipn\Listener;

class GuzzleArrayListenerBuilderContext extends FeatureContext
{
    protected function getListener() : Listener
    {
        $listenerBuilder = new ArrayListenerBuilder();

        $listenerBuilder->setServiceEndpoint(
            $this->getServiceEndpoint()
        );
        $listenerBuilder->setData($this->ipnMessageData);

        return $listenerBuilder->build();
    }
}
