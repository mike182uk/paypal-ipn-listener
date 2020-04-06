<?php

namespace Mdb\PayPal\Ipn\Event;

use Mdb\PayPal\Ipn\Message;
use Symfony\Contracts\EventDispatcher\Event;

abstract class MessageVerificationEvent extends Event
{
    /**
     * @var Message
     */
    private $message;

    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    public function getMessage() : Message
    {
        return $this->message;
    }
}
