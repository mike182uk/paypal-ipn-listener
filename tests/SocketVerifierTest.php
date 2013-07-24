<?php

use PayPal\Ipn\Verifier\SocketVerifier;

class SocketVerifierTest extends PHPUnit_Framework_TestCase
{
    public function testExtendsVerifier()
    {
        $verifier = new SocketVerifier;
        $this->assertInstanceOf('PayPal\Ipn\Verifier', $verifier);
    }

    public function testGetRequestUri()
    {
        $verifier = new SocketVerifier;
        $host = $verifier->getHost();

        $this->assertEquals(sprintf('ssl://%s', $host), $verifier->getRequestUri());

        $verifier->secure(false);

        $this->assertEquals($host, $verifier->getRequestUri());
    }

    public function testSendVerificationRequest()
    {
        $verifier = new SocketVerifier;
        $verifier->setEnvironment('sandbox');

        $verificationResponse = $verifier->sendVerificationRequest();

        $this->assertContains('INVALID', $verificationResponse->getBody());
    }
}
