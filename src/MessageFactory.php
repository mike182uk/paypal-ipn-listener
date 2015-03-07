<?php

namespace Mdb\PayPal\Ipn;

interface MessageFactory
{
    /**
     * @return Message
     */
    public function createMessage();
}
