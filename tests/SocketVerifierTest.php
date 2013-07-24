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

        $verifier = $this->getMock(
            'PayPal\Ipn\Verifier\SocketVerifier',
            array('getRequestUri')
        );

        $verifier->expects($this->any())
                 ->method('getRequestUri')
                 ->will($this->returnValue('//INVALID URI'));

        $this->setExpectedException('RuntimeException');

        $verificationResponse = $verifier->sendVerificationRequest();
    }
}
