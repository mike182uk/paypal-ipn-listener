<?php

use \PayPal\Ipn\Request as Request;

class CurlRequestTest extends PHPUnit_Framework_TestCase
{
    protected $curlRequestObj;

    public function setUp()
    {
        $this->curlRequestObj = new Request\cURL();
    }

    public function testExtendsRequestObject()
    {
        $this->assertInstanceOf('\PayPal\Ipn\Request', $this->curlRequestObj);
    }
}
