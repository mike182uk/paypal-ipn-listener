<?php

namespace Mdb\PayPal\Ipn;

class StreamWrapperMessageFactory implements MessageFactory
{
    /**
     * @var StreamWrapperAdapter
     */
    private $streamWrapperAdapter;

    /**
     * @param StreamWrapperAdapter $streamWrapperAdapter
     */
    public function __construct(StreamWrapperAdapter $streamWrapperAdapter)
    {
        $this->streamWrapperAdapter = $streamWrapperAdapter;
    }

    /**
     * @return Message
     */
    public function createMessage()
    {
        $streamContents = $this->streamWrapperAdapter->getInputStreamContents();

        $data  = array();
        $keyValuePairs = explode('&', $streamContents);

        foreach ($keyValuePairs as $keyValuePair) {
            list($k, $v) = explode('=', $keyValuePair);

            $data[$k] = urldecode($v);
        }

        return new Message($data);
    }
}
