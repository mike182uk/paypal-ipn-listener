<?php

namespace Mdb\PayPal\Ipn\MessageFactory;

use Mdb\PayPal\Ipn\InputStream;
use Mdb\PayPal\Ipn\Message;
use Mdb\PayPal\Ipn\MessageFactory;

class InputStreamMessageFactory implements MessageFactory
{
    /**
     * @var InputStream
     */
    private $inputStream;

    public function __construct(InputStream $inputStream)
    {
        $this->inputStream = $inputStream;
    }

    public function createMessage() : Message
    {
        $streamContents = $this->inputStream->getContents();

        return new Message($streamContents);
    }
}
