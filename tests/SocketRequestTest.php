<?php

use \PayPal\Ipn\Request as Request;

class SocketRequestTest extends PHPUnit_Framework_TestCase
{
    protected $socketRequestObj;

    public function setUp()
    {
        $this->socketRequestObj = new Request\Socket();
    }

    public function testExtendsRequestObject()
    {
        $this->assertInstanceOf('\PayPal\Ipn\Request', $this->socketRequestObj);
    }

    public function testGetRequestUri()
    {
        $uri = $this->socketRequestObj->getRequestUri();
        $isValidUri = (filter_var($uri, FILTER_VALIDATE_URL)) ? true : false;
        $this->assertTrue($isValidUri);
    }

    public function testExceptionIsThrownOnSocketError()
    {
        $this->setExpectedException('\PayPal\Ipn\Exception\SocketRequestException');

        $mockCurlRequest = new MockSocketRequest();
        
        $mockCurlRequest->send();
    }

    public function testSend()
    {
        $this->socketRequestObj->send();

        $this->assertEquals(200, $this->socketRequestObj->getResponse()->getStatus());
    }
}

class MockSocketRequest extends \PayPal\Ipn\Request\Socket
{
    public function getRequestUri()
    {
        return '';
    }
}
