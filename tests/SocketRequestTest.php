<?php

use PayPal\Ipn\Request\Socket as SocketRequest;

class SocketRequestTest extends PHPUnit_Framework_TestCase
{
    protected $request;

    public function setUp()
    {
        $this->request = new SocketRequest();
    }

    public function testExtendsRequestObject()
    {
        $this->assertInstanceOf('PayPal\Ipn\Request', $this->request);
    }

    public function testGetRequestUri()
    {
        $uri = $this->request->getRequestUri();
        $isValidUri = (filter_var($uri, FILTER_VALIDATE_URL)) ? true : false;
        $this->assertTrue($isValidUri);
    }

    public function testExceptionIsThrownOnSocketError()
    {
        $this->setExpectedException('PayPal\Ipn\Exception\SocketRequestException');

        $mockCurlRequest = new MockSocketRequest();

        $mockCurlRequest->send();
    }

    public function testSend()
    {
        $this->request->send();

        $this->assertEquals(200, $this->request->getResponse()->getStatusCode());
    }
}

class MockSocketRequest extends SocketRequest
{
    public function getRequestUri()
    {
        return '';
    }
}
