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

    public function testExceptionIsThrownOnCurlError()
    {
    	$this->setExpectedException('\PayPal\Ipn\Exception\CurlRequestException');

    	$mockCurlRequest = new MockCurlRequest();
    	
    	$mockCurlRequest->send();
    }

    public function testSend()
    {
    	$this->curlRequestObj->send();

    	$this->assertEquals(200, $this->curlRequestObj->getResponse()->getStatus());
    }
}

class MockCurlRequest extends \PayPal\Ipn\Request\cURL
{
    public function getRequestUri()
    {
    	return '';
    }
}
