<?php

namespace Mdb\PayPal\Ipn\MessageFactory;

use Mdb\PayPal\Ipn\Message;
use Mdb\PayPal\Ipn\MessageFactory;

class ArrayMessageFactory implements MessageFactory
{
    /**
     * @var array
     */
    private $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function createMessage() : Message
    {
        return new Message($this->data);
    }
}
