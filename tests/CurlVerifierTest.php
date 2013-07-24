<?php

use PayPal\Ipn\Verifier\CurlVerifier;

class CurlVerifierTest extends PHPUnit_Framework_TestCase
{
    public function testExtendsVerifier()
    {
        $verifier = new CurlVerifier;
        $this->assertInstanceOf('PayPal\Ipn\Verifier', $verifier);
    }

    public function testConstructorChecksCurlIsEnabled()
    {
        // We need to simulate curl being disabled.
        // We cannot dynamically unload an extension so we have to
        // stub the curlEnabled method to always return false.
        $verifier = $this->getMockBuilder('PayPal\Ipn\Verifier\CurlVerifier')
                         ->setMethods(array('curlEnabled'))
                         ->disableOriginalConstructor()
                         ->getMock();

        $verifier->expects($this->any())
                 ->method('curlEnabled')
                 ->will($this->returnValue(false));

        $this->setExpectedException('RuntimeException');

        $verifier->__construct();
    }

    public function testSetFollowLocation()
    {
        $verifier = new CurlVerifier;

        $verifier->followLocation('VALUE THAT SHOULD BE CAST TO TRUE');
        $this->assertAttributeEquals(true, 'followLocation', $verifier);

        $verifier->followLocation(false);
        $this->assertAttributeEquals(false, 'followLocation', $verifier);
    }

    public function testForceSSLv3()
    {
        $verifier = new CurlVerifier;

        $verifier->forceSSLv3('VALUE THAT SHOULD BE CAST TO TRUE');
        $this->assertAttributeEquals(true, 'forceSSLv3', $verifier);

        $verifier->forceSSLv3(false);
        $this->assertAttributeEquals(false, 'forceSSLv3', $verifier);
    }

    public function testSendVerificationRequest()
    {
        $verifier = new CurlVerifier;
        $verifier->setEnvironment('sandbox');

        $verificationResponse = $verifier->sendVerificationRequest();
        $this->assertContains('INVALID', $verificationResponse->getBody());

        $verifier = $this->getMock(
            'PayPal\Ipn\Verifier\CurlVerifier',
            array('getRequestUri')
        );

        $verifier->expects($this->any())
                 ->method('getRequestUri')
                 ->will($this->returnValue('//INVALID URI'));

        $this->setExpectedException('RuntimeException');

        $verificationResponse = $verifier->sendVerificationRequest();
    }
}
