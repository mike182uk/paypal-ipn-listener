<?php

namespace Mdb\PayPal\Ipn\Event;

use Mdb\PayPal\Ipn\Message;

class MessageVerificationFailureEvent extends MessageVerificationEvent
{
    /**
     * @var string
     */
    private $error;

    /**
     * @param Message $message
     * @param $error
     */
    public function __construct(Message $message, $error)
    {
        $this->error = $error;

        parent::__construct($message);
    }

    /**
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }
}
