<?php

namespace Mdb\PayPal\Ipn\ListenerBuilder\Guzzle;

use Mdb\PayPal\Ipn\ListenerBuilder\GuzzleListenerBuilder;
use Mdb\PayPal\Ipn\MessageFactory\ArrayMessageFactory;

class ArrayListenerBuilder extends GuzzleListenerBuilder
{
    /**
     * @var array
     */
    private $data = [];

    /**
     * @param array $data
     */
    public function setData(array $data)
    {
        $this->data = $data;
    }

    /**
     * {@inheritdoc}
     */
    protected function getMessageFactory()
    {
        return new ArrayMessageFactory($this->data);
    }
}
