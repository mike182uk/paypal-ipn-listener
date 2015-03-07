<?php

namespace Mdb\PayPal\Ipn\Event;

use Mdb\PayPal\Ipn\Message;

interface MessageVerificationEvent
{
    /**
     * @param Message $message
     */
    public function __construct(Message $message);

    /**
     * @return Message
     */
    public function getMessage();
}
