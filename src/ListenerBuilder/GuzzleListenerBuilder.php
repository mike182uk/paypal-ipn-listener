<?php

namespace Mdb\PayPal\Ipn\ListenerBuilder;

use GuzzleHttp\Client;
use Mdb\PayPal\Ipn\ListenerBuilder;
use Mdb\PayPal\Ipn\Service;
use Mdb\PayPal\Ipn\Service\GuzzleService;

abstract class GuzzleListenerBuilder extends ListenerBuilder
{
    use ModeDependentServiceEndpoint;

    /**
     * {@inheritdoc}
     */
    protected function getService() : Service
    {
        return new GuzzleService(
            new Client(),
            $this->getServiceEndpoint()
        );
    }
}
