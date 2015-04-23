<?php

namespace Mdb\PayPal\Ipn;

class PhpInputStreamMessageFactory implements MessageFactory
{
    /**
     * @var PhpInputStreamAdapter
     */
    private $phpInputStreamAdapter;

    /**
     * @param PhpInputStreamAdapter $phpInputStreamAdapter
     */
    public function __construct(PhpInputStreamAdapter $phpInputStreamAdapter)
    {
        $this->phpInputStreamAdapter = $phpInputStreamAdapter;
    }

    /**
     * @return Message
     */
    public function createMessage()
    {
        $streamContents = $this->phpInputStreamAdapter->getContents();

        return new Message($streamContents);
    }
}
