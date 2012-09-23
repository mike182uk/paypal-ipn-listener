<?php

class RequestTest extends PHPUnit_Framework_TestCase
{
    protected $mockRequestObj;

    public function setUp()
    {
        $this->mockRequestObj = new MockRequest;
    }

    public function testGetAndSetHost()
    {
        $host = 'sandbox';

        $this->mockRequestObj->setHost($host);

        $this->assertEquals(\PayPal\Ipn\Request::SANDBOX_HOST, $this->mockRequestObj->getHost());
    }

    public function testDefaultHostGetsSet()
    {
        //test if no mode is passed
        $this->mockRequestObj->setHost();
        $this->assertEquals(\PayPal\Ipn\Request::PRODUCTION_HOST, $this->mockRequestObj->getHost());

        //test if invalid mode is passed
        $this->mockRequestObj->setHost('invalid-mode');
        $this->assertEquals(\PayPal\Ipn\Request::SANDBOX_HOST, $this->mockRequestObj->getHost());
    }

    public function testSetAndGetData()
    {
        $data = array('name' => 'item name', 'price' => '9.99');

        $this->mockRequestObj->setData($data);

        $this->assertSame($data, $this->mockRequestObj->getData());
    }

    public function testDefaultDataGetsSet()
    {
        $_POST = array();
        $_POST['name'] = 'item name';
        $_POST['price'] = '9.99';

        $this->mockRequestObj->setData();

        $this->assertSame($_POST, $this->mockRequestObj->getData());
    }

    public function testEncodeData()
    {
        $data = array('name' => 'item name', 'price' => '9.99');

        $this->mockRequestObj->setData($data);

        $encodedData = $this->mockRequestObj->getEncodedData();

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
        $this->mockRequestObj->setTimeout(120);

        $this->assertSame(120, $this->mockRequestObj->getTimeoutValue());
    }

    public function testSecure()
    {
        $this->mockRequestObj->secure(true);

        $this->assertTrue($this->mockRequestObj->getSecureValue());
    }

    public function testGetResponse()
    {
        $this->assertInstanceOf('\PayPal\Ipn\Response', $this->mockRequestObj->getResponse());
    }

    public function testConstructorDefaults()
    {
        $_POST = array();
        $_POST['name'] = 'item name';
        $_POST['price'] = '9.99';

        $req = new MockRequest();

        $this->assertInstanceOf('\PayPal\Ipn\Response', $req->getResponse());
        $this->assertSame($_POST, $req->getData());
    }

    public function testConstructorWithCustomArgValues()
    {
        $resp = new MockResponse();
        $data = array('name' => 'item name', 'price' => '9.99');

        $req = new MockRequest($data, $resp);

        $this->assertSame($data, $req->getData());
        $this->assertSame($resp, $req->getResponse());
    }
}

class MockRequest extends \PayPal\Ipn\Request
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

class MockResponse extends \PayPal\Ipn\Response
{

}
