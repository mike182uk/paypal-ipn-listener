<?php

use PayPal\Ipn\Listener;
use PayPal\Ipn\Message;
use PayPal\Ipn\Verifier\CurlVerifier;
use PayPal\Ipn\VerificationResponse;

class ListenerTest extends PHPUnit_Framework_TestCase
{
    public function testGetSetVerifier()
    {
        $listener = new Listener;
        $verifier = new CurlVerifier;

        $listener->setVerifier($verifier);

        $this->assertSame($verifier, $listener->getVerifier());
    }

    public function testOnVerifiedIpn()
    {
        $listener = new Listener;
        $listener->onVerifiedIpn(function(){
            return 'Foo';
        });

        $listener->onVerifiedIpn(function(){
            return 'Bar';
        });

        $verifiedIpnCallbackStack = PHPUnit_Framework_Assert::readAttribute($listener, 'verifiedIpnCallbackStack');

        $this->assertEquals(2, count($verifiedIpnCallbackStack));
        $this->assertEquals('Foo', $verifiedIpnCallbackStack[0]());
        $this->assertEquals('Bar', $verifiedIpnCallbackStack[1]());
    }

    public function testOnInvalidIpn()
    {
        $listener = new Listener;
        $listener->onInvalidIpn(function(){
            return 'Foo';
        });

        $listener->onInvalidIpn(function(){
            return 'Bar';
        });

        $invalidIpnCallbackStack = PHPUnit_Framework_Assert::readAttribute($listener, 'invalidIpnCallbackStack');

        $this->assertEquals(2, count($invalidIpnCallbackStack));
        $this->assertEquals('Foo', $invalidIpnCallbackStack[0]());
        $this->assertEquals('Bar', $invalidIpnCallbackStack[1]());
    }

    public function testGetReport()
    {
        $listener = new Listener;
        $verifier = new CurlVerifier;
        $ipnMessage = Message::createFromGlobals();

        $verifier->setIpnMessage($ipnMessage);
        $verifier->setEnvironment('sandbox');
        $listener->setVerifier($verifier);

        // test report states verification request was not made
        $report = $listener->getReport();
        $this->assertContains('VERIFICATION REQUEST NOT MADE', $report);

        // test get report throws exception when there is no verifier
        $listener = new Listener;

        $this->setExpectedException('RuntimeException');

        $listener->getReport();
    }

    public function testProcessIpnReturnsBool()
    {
        $listener = new Listener;
        $verifier = $this->getMockForAbstractClass('PayPal\Ipn\Verifier');
        $ipnMessage = Message::createFromGlobals();

        $verifier->expects($this->at(0))
                 ->method('sendVerificationRequest')
                 ->will($this->returnValue(new VerificationResponse('INVALID', 200)));

        $verifier->expects($this->at(1))
                 ->method('sendVerificationRequest')
                 ->will($this->returnValue(new VerificationResponse('VERIFIED', 200)));

        $verifier->setIpnMessage($ipnMessage);
        $verifier->setEnvironment('sandbox');
        $listener->setVerifier($verifier);

        $this->assertEquals(false, $listener->processIpn());
        $this->assertEquals(true, $listener->processIpn());
    }

    public function testProcessIpnCallbackExecution()
    {
        $listener = new Listener;
        $verifier = $this->getMockForAbstractClass('PayPal\Ipn\Verifier');
        $ipnMessage = Message::createFromGlobals();

        $verifier->expects($this->at(0))
                 ->method('sendVerificationRequest')
                 ->will($this->returnValue(new VerificationResponse('INVALID', 200)));

        $verifier->expects($this->at(1))
                 ->method('sendVerificationRequest')
                 ->will($this->returnValue(new VerificationResponse('VERIFIED', 200)));

        $verifier->expects($this->at(2))
                 ->method('sendVerificationRequest')
                 ->will($this->returnValue(new VerificationResponse('VERIFIED', 200)));

        $verifier->setIpnMessage($ipnMessage);
        $verifier->setEnvironment('sandbox');
        $listener->setVerifier($verifier);

        $counter = 0;

        $listener->onVerifiedIpn(function() use (&$counter) {
            $counter++;
        });

        $listener->onVerifiedIpn(function() use (&$counter) {
            $counter++;
        });

        $listener->onInvalidIpn(function() use (&$counter) {
            $counter++;
        });

        $listener->onInvalidIpn(function() use (&$counter) {
            $counter++;
        });

        $listener->processIpn(); // verified callbacks
        $listener->processIpn(); // invalid callbacks

        $this->assertEquals(4, $counter);

        $listener->processIpn(false); // callbacks should not be executed

        $this->assertEquals(4, $counter);
    }

    public function testProcessIpnThrowsExceptionOnUnsetVerifier()
    {
        $listener = new Listener;

        $this->setExpectedException('RuntimeException');

        $listener->processIpn();
    }

    public function testListen()
    {
        $listener = new Listener;
        $verifier = $this->getMockForAbstractClass('PayPal\Ipn\Verifier');
        $ipnMessage = Message::createFromGlobals();

        $verifier->expects($this->at(0))
                 ->method('sendVerificationRequest')
                 ->will($this->returnValue(new VerificationResponse('INVALID', 200)));

        $verifier->expects($this->at(1))
                 ->method('sendVerificationRequest')
                 ->will($this->returnValue(new VerificationResponse('VERIFIED', 200)));

        $verifier->setIpnMessage($ipnMessage);
        $verifier->setEnvironment('sandbox');
        $listener->setVerifier($verifier);

        $status = null;
        for ($i = 0; $i <= 1; $i++) {
            $listener->listen(function() use (&$status) {
                $status = true;
            }, function() use (&$status) {
                $status = false;
            });

            if ($i == 0) {
                $this->assertEquals(false, $status);
            } elseif ($i == 1) {
                $this->assertEquals(true, $status);
            }
        }
    }
}
