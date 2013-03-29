<?php

use PayPal\Ipn\Request as IpnRequest;
use PayPal\Ipn\Response as IpnResponse;

class RequestTest extends PHPUnit_Framework_TestCase
{
    protected $request;

    public function setUp()
    {
        $this->request = new MockRequest;
    }

    public function testGetAndSetHost()
    {
        $host = 'sandbox';

        $this->request->setHost($host);

        $this->assertEquals(PayPal\Ipn\Request::SANDBOX_HOST, $this->request->getHost());
    }

    public function testDefaultHostGetsSet()
    {
        //test if no mode is passed
        $this->request->setHost();
        $this->assertEquals(PayPal\Ipn\Request::PRODUCTION_HOST, $this->request->getHost());

        //test if invalid mode is passed
        $this->request->setHost('invalid-mode');
        $this->assertEquals(PayPal\Ipn\Request::SANDBOX_HOST, $this->request->getHost());
    }

    public function testSetAndGetData()
    {
        $data = array('name' => 'item name', 'price' => '9.99');

        $this->request->setData($data);

        $this->assertSame($data, $this->request->getData());
    }

    public function testDefaultDataGetsSet()
    {
        $_POST = array();
        $_POST['name'] = 'item name';
        $_POST['price'] = '9.99';

        $this->request->setData();

        $this->assertSame($_POST, $this->request->getData());
    }

    public function testEncodeData()
    {
        $data = array('name' => 'item name', 'price' => '9.99');

        $this->request->setData($data);

        $encodedData = $this->request->getEncodedData();

        //make sure it is a string
        $this->assertInternalType('string',$encodedData);

        //make sure it starts with cmd=_notify-validate
        $this->assertStringStartsWith('cmd=_notify-validate', $encodedData);

        //make sure it ends with the data supplied as a string
        $dataStr = 'name=' . urlencode('item name') . '&price=' . urlencode('9.99');
        $this->assertStringEndsWith($dataStr, $encodedData);
    }

    public function testSetTimeout()
    {
        $this->request->setTimeout(120);

        $this->assertSame(120, $this->request->getTimeoutValue());
    }

    public function testSecure()
    {
        $this->request->secure(true);

        $this->assertTrue($this->request->getSecureValue());
    }

    public function testGetResponse()
    {
        $this->assertInstanceOf('PayPal\Ipn\Response', $this->request->getResponse());
    }

    public function testConstructorDefaults()
    {
        $_POST = array();
        $_POST['name'] = 'item name';
        $_POST['price'] = '9.99';

        $req = new MockRequest();

        $this->assertInstanceOf('PayPal\Ipn\Response', $req->getResponse());
        $this->assertSame($_POST, $req->getData());
    }

    public function testConstructorWithCustomArgValues()
    {
        $resp = new CustomResponse();
        $data = array('name' => 'item name', 'price' => '9.99');

        $req = new MockRequest($data, $resp);

        $this->assertSame($data, $req->getData());
        $this->assertSame($resp, $req->getResponse());
    }
}

class MockRequest extends IpnRequest
{

    public function getTimeoutValue()
    {
        return $this->timeout;
    }

    public function getSecureValue()
    {
        return $this->useSSL;
    }

    public function send() { }
}

class CustomResponse extends IpnResponse
{

}
