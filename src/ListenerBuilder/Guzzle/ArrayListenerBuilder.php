<?php

namespace Mdb\PayPal\Ipn\ListenerBuilder\Guzzle;

use Mdb\PayPal\Ipn\ListenerBuilder\GuzzleListenerBuilder;
use Mdb\PayPal\Ipn\MessageFactory;
use Mdb\PayPal\Ipn\MessageFactory\ArrayMessageFactory;

class ArrayListenerBuilder extends GuzzleListenerBuilder
{
    /**
     * @var array
     */
    private $data = [];

    public function setData(array $data) : void
    {
        $this->data = $data;
    }

    protected function getMessageFactory() : MessageFactory
    {
        return new ArrayMessageFactory($this->data);
    }
}
