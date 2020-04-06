<?php

namespace Mdb\PayPal\Ipn\Event;

use Mdb\PayPal\Ipn\Message;

class MessageVerificationFailureEvent extends MessageVerificationEvent
{
    /**
     * @var string
     */
    private $error;

    public function __construct(Message $message, string $error)
    {
        $this->error = $error;

        parent::__construct($message);
    }

    public function getError() : string
    {
        return $this->error;
    }
}
