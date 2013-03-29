<?php

use PayPal\Ipn\Response as IpnResponse;

class ResponseTest extends PHPUnit_Framework_TestCase
{
    protected $response;

    public function setUp()
    {
        $this->response = new IpnResponse();
    }

    public function testExtendsResponseObject()
    {
        $this->assertInstanceOf('PayPal\Ipn\Response', $this->response);
    }

    public function testGetAndSetBody()
    {
        $content = 'TEST BODY CONTENT';

        $this->response->setBody($content);

        $this->assertEquals($content, $this->response->getBody());
    }

    public function testGetAndSetStatusCode()
    {
        $content = 200;

        $this->response->setStatusCode($content);

        $this->assertEquals($content, $this->response->getStatusCode());
    }
}
