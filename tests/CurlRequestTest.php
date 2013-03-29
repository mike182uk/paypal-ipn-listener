<?php

use PayPal\Ipn\Request;
use PayPal\Ipn\Request\Curl as CurlRequest;

class CurlRequestTest extends PHPUnit_Framework_TestCase
{
    protected $request;

    public function setUp()
    {
        $this->request = new CurlRequest();
    }

    public function testExtendsRequestObject()
    {
        $this->assertInstanceOf('PayPal\Ipn\Request', $this->request);
    }

    public function testExceptionIsThrownOnCurlError()
    {
    	$this->setExpectedException('PayPal\Ipn\Exception\CurlRequestException');

    	$mockCurlRequest = new MockCurlRequest();

    	$mockCurlRequest->send();
    }

    public function testSend()
    {
    	$this->request->send();

    	$this->assertEquals(200, $this->request->getResponse()->getStatusCode());
    }
}

class MockCurlRequest extends CurlRequest
{
    public function getRequestUri()
    {
    	return '';
    }
}
