<?php

namespace Mdb\PayPal\Ipn;

class ServiceResponse
{
    /**
     * @var string
     */
    private $body;

    /**
     * @param $body
     */
    public function __construct($body)
    {
        $this->body = $body;
    }

    public function getBody() : string
    {
        return $this->body;
    }
}
