<?php

namespace Mdb\PayPal\Ipn;

class InputStream
{
    public function getContents() : string
    {
        return file_get_contents('php://input');
    }
}
