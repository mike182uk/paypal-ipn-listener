<?php

use PayPal\Ipn\Listener;
use PayPal\Ipn\Request as IpnRequest;
use PayPal\Ipn\Request\Curl as CurlRequest;

class ListenerTest extends PHPUnit_Framework_TestCase
{
    protected $listener;

    public function setUp()
    {
        $request = new CurlRequest();

        $this->listener = new MockListener($request);
    }

    public function testSetMode()
    {
        $mode = 'sandbox';

        $this->listener->setMode($mode);

        $this->assertEquals(IpnRequest::SANDBOX_HOST, $this->listener->getHost());
    }

    public function testGetReport()
    {
        $report = $this->listener->getReport();
        $this->assertRegexp('/RESPONSE STATUS:/', $report);
        $this->assertRegexp('/RESPONSE BODY:/', $report);
        $this->assertRegexp('/cmd=_notify-validate/', $report);
    }

    public function testVerifyIpnInvalid()
    {
        $this->assertEquals(false, $this->listener->verifyIpn());
    }
}

class MockListener extends Listener
{
    public function getHost()
    {
        return $this->request->getHost();
    }

    public function getReport()
    {
        ini_set('date.timezone', 'Europe/London'); // phpunit forces me to do this?!?
        return parent::getReport();
    }
}
