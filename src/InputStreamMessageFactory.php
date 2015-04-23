<?php

namespace Mdb\PayPal\Ipn;

class InputStreamMessageFactory implements MessageFactory
{
    /**
     * @var InputStream
     */
    private $inputStream;

    /**
     * @param InputStream $inputStream
     */
    public function __construct(InputStream $inputStream)
    {
        $this->inputStream = $inputStream;
    }

    /**
     * @return Message
     */
    public function createMessage()
    {
        $streamContents = $this->inputStream->getContents();

        return new Message($streamContents);
    }
}
