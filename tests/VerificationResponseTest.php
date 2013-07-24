<?php

use PayPal\Ipn\VerificationResponse;

class VerificationResponseTest extends PHPUnit_Framework_TestCase
{
    protected $verificationResponse;

    public function setUp()
    {
        $this->verificationResponse = new VerificationResponse('body', '200');
    }

    public function testGetSetBody()
    {
        $this->assertEquals('body', $this->verificationResponse->getBody());
    }

    public function testGetSetStatusCode()
    {
        $this->assertEquals(200, $this->verificationResponse->getStatusCode());
        $this->assertInternalType('integer', $this->verificationResponse->getStatusCode());
    }
}
