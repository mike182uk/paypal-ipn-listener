<?php

namespace Mdb\PayPal\Ipn\ListenerBuilder\Guzzle;

use Mdb\PayPal\Ipn\InputStream;
use Mdb\PayPal\Ipn\ListenerBuilder\GuzzleListenerBuilder;
use Mdb\PayPal\Ipn\MessageFactory\InputStreamMessageFactory;

class InputStreamListenerBuilder extends GuzzleListenerBuilder
{
    /**
     * {@inheritdoc}
     */
    protected function getMessageFactory()
    {
        return new InputStreamMessageFactory(new InputStream());
    }
}
