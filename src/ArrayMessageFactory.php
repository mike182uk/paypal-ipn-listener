<?php

namespace Mdb\PayPal\Ipn;

class ArrayMessageFactory implements MessageFactory
{
    /**
     * @var array
     */
    private $data;

    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * {@inheritdoc}
     */
    public function createMessage()
    {
        return new Message($this->data);
    }
}
