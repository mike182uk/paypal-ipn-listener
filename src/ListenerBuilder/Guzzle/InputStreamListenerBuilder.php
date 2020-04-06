<?php

namespace Mdb\PayPal\Ipn\ListenerBuilder\Guzzle;

use Mdb\PayPal\Ipn\InputStream;
use Mdb\PayPal\Ipn\ListenerBuilder\GuzzleListenerBuilder;
use Mdb\PayPal\Ipn\MessageFactory;
use Mdb\PayPal\Ipn\MessageFactory\InputStreamMessageFactory;

class InputStreamListenerBuilder extends GuzzleListenerBuilder
{
    protected function getMessageFactory() : MessageFactory
    {
        return new InputStreamMessageFactory(new InputStream());
    }
}
