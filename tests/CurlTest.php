<?php

use \PayPal\Ipn\Request as Request;

class CurlTest extends PHPUnit_Framework_TestCase
{
    protected $curlRequestObj;

    public function setUp()
    {
        $this->curlRequestObj = new Request\cURL();
    }

    public function testExtendsRequestObject()
    {
        $this->assertEquals(true, ($this->curlRequestObj instanceof Request));
    }

    

}
