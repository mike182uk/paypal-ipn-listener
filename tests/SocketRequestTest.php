<?php

use \PayPal\Ipn\Request as Request;

class SocketRequestTest extends PHPUnit_Framework_TestCase
{
    protected $socketRequestObj;

    public function setUp()
    {
        $this->curlRequestObj = new Request\Socket();
    }

    public function testExtendsRequestObject()
    {
        $this->assertEquals(true, ($this->curlRequestObj instanceof Request));
    }

}
