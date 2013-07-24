<?php

use PayPal\Ipn\Verifier;
use PayPal\Ipn\Message;
use PayPal\Ipn\VerificationResponse;

class VerifierTest extends PHPUnit_Framework_TestCase
{
    public function testSetGetIpnMessage()
    {
        $verifier = $this->getMockForAbstractClass('PayPal\Ipn\Verifier');
        $message = new Message(array());

        $verifier->setIpnMessage($message);

        $this->assertSame($message, $verifier->getIpnMessage());
    }

    public function testSetGetEnvironment()
    {
        $verifier = $this->getMockForAbstractClass('PayPal\Ipn\Verifier');

        $verifier->setEnvironment('sandbox');
        $this->assertEquals('sandbox', $verifier->getEnvironment());

        $verifier->setEnvironment('production');
        $this->assertEquals('production', $verifier->getEnvironment());

        $verifier->setEnvironment('Invalid Environment Should Default To Sandbox');
        $this->assertEquals('sandbox', $verifier->getEnvironment());
    }

    public function testGetHost()
    {
        $verifier = $this->getMockForAbstractClass('PayPal\Ipn\Verifier');

        $this->assertEquals($verifier::PRODUCTION_HOST, $verifier->getHost());
    }

    public function testGetVerificationResponse()
    {
        $verifier = $this->getMockForAbstractClass('PayPal\Ipn\Verifier');
        $verificationResponse = new VerificationResponse('body', 200);

        $verifierReflection = new ReflectionObject($verifier);
        $property = $verifierReflection->getProperty('verificationResponse');
        $property->setAccessible(true);
        $property->setValue($verifier, $verificationResponse);

        $this->assertSame($verificationResponse, $verifier->getVerificationResponse());
    }

    public function testSetTimeout()
    {
        $verifier = $this->getMockForAbstractClass('PayPal\Ipn\Verifier');

        $verifier->setTimeout('60');
        $this->assertAttributeEquals(60, 'timeout', $verifier);
        $this->assertAttributeInternalType('integer', 'timeout', $verifier);
    }

    public function testSetSecure()
    {
        $verifier = $this->getMockForAbstractClass('PayPal\Ipn\Verifier');

        $verifier->secure('VALUE THAT SHOULD BE CAST TO TRUE');
        $this->assertAttributeEquals(true, 'useSSL', $verifier);

        $verifier->secure(false);
        $this->assertAttributeEquals(false, 'useSSL', $verifier);
    }

    public function testGetRequestUri()
    {
        $verifier = $this->getMockForAbstractClass('PayPal\Ipn\Verifier');
        $host = $verifier->getHost();

        $this->assertEquals(sprintf('https://%s/cgi-bin/webscr', $host), $verifier->getRequestUri());

        $verifier->secure(false);

        $this->assertEquals(sprintf('http://%s/cgi-bin/webscr', $host), $verifier->getRequestUri());
    }

    public function testVerify()
    {
        $verifier = $this->getMockForAbstractClass('PayPal\Ipn\Verifier');
        $message = new Message(array());
        $verifier->setIpnMessage($message);

        $verifier->expects($this->at(0))
                 ->method('sendVerificationRequest')
                 ->will($this->returnValue(new VerificationResponse('INVALID', 200)));

        $verifier->expects($this->at(1))
                 ->method('sendVerificationRequest')
                 ->will($this->returnValue(new VerificationResponse('VERIFIED', 200)));

        $verify = $verifier->verify();

        $this->assertEquals('INVALID', $verifier->getVerificationResponse()->getBody());
        $this->assertEquals(false, $verify);

        $verify = $verifier->verify();

        $this->assertEquals('VERIFIED', $verifier->getVerificationResponse()->getBody());
        $this->assertEquals(true, $verify);
    }

    public function testVerifyThrowsExceptionOnUnsetIpnMessage()
    {
        $verifier = $this->getMockForAbstractClass('PayPal\Ipn\Verifier');

        $this->setExpectedException('RuntimeException');

        $verifier->verify();
    }

    public function testVerifyThrowsExceptionOnUnexpectedResponseBody()
    {
        $verifier = $this->getMockForAbstractClass('PayPal\Ipn\Verifier');
        $message = new Message(array());
        $verifier->setIpnMessage($message);

        $verifier->expects($this->any())
                 ->method('sendVerificationRequest')
                 ->will($this->returnValue(new VerificationResponse('', 200)));

        $this->setExpectedException('UnexpectedValueException');

        $verifier->verify();
    }

    public function testVerifyThrowsExceptionOnUnexpectedStatusCode()
    {
        $verifier = $this->getMockForAbstractClass('PayPal\Ipn\Verifier');
        $message = new Message(array());
        $verifier->setIpnMessage($message);

        $verifier->expects($this->any())
                 ->method('sendVerificationRequest')
                 ->will($this->returnValue(new VerificationResponse('INVALID', 300)));

        $this->setExpectedException('UnexpectedValueException');

        $verifier->verify();
    }
}
