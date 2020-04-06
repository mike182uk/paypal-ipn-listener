<?php

namespace Mdb\PayPal\Ipn;

interface MessageFactory
{
    public function createMessage() : Message;
}
