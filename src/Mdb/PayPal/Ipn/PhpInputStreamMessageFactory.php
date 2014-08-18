<?php

namespace Mdb\PayPal\Ipn;

class PhpInputStreamMessageFactory implements MessageFactory
{
    /**
     * @var PhpInputStreamAdapter
     */
    private $phpInputStreamAdapter;

    /**
     * @param PhpInputStreamAdapter $streamWrapperAdapter
     */
    public function __construct(PhpInputStreamAdapter $streamWrapperAdapter)
    {
        $this->phpInputStreamAdapter = $streamWrapperAdapter;
    }

    /**
     * @return Message
     */
    public function createMessage()
    {
        $streamContents = $this->phpInputStreamAdapter->getContents();

        $data  = array();
        $keyValuePairs = explode('&', $streamContents);

        foreach ($keyValuePairs as $keyValuePair) {
            list($k, $v) = explode('=', $keyValuePair);

            $data[$k] = urldecode($v);
        }

        return new Message($data);
    }
}
