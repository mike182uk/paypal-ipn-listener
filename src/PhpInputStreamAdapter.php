<?php

namespace Mdb\PayPal\Ipn;

class PhpInputStreamAdapter
{
    /**
     * @return string
     */
    public function getContents()
    {
        return file_get_contents('php://input');
    }
}
