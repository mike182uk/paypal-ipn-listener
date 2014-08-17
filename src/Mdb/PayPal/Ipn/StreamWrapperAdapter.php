<?php

namespace Mdb\PayPal\Ipn;

class StreamWrapperAdapter
{
    /**
     * @return string
     */
    public function getInputStreamContents()
    {
        return file_get_contents('php://input');
    }
}
