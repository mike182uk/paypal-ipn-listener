<?php

use PayPal\Ipn\Message;

class MessageTest extends PHPUnit_Framework_TestCase
{
    public function testArrayAccess()
    {
        $data = array(
            'param1' => 'value1',
            'param2' => 'value2'
        );

        $message = new Message($data);

        $this->assertInstanceOf('ArrayAccess', $message);

        unset($message['param1']);
        $message['param3'] = 'value3';

        $this->assertEquals(true, isset($message['param2'])); // offsetExists
        $this->assertEquals('value2', $message['param2']); // offsetGet
        $this->assertEquals('value3', $message['param3']); // offsetSet
        $this->assertArrayNotHasKey('param1', $message); // offsetUnset
    }

    public function testGetIterator()
    {
        $message = new Message(array());

        $this->assertInstanceOf('ArrayIterator', $message->getIterator());
    }

    public function testToString()
    {
        $data = array(
            'param1' => 'value1',
            'param2' => 'value2'
        );

        $dataStr = '';
        foreach ($data as $k => $v) {
            $dataStr .= sprintf('%s=%s&', $k, $v);
        }

        $message = new Message($data);

        $this->assertEquals(rtrim($dataStr, '&'), (string) $message);
    }

    public function testConstructor()
    {
        $data = array(
            'param1' => 'value1',
            'param2' => 'value2'
        );

        $message = new Message($data);

        $this->assertInstanceOf('PayPal\Ipn\Message', $message);
        $this->assertEquals('value2', $message['param2']);
    }

    public function testCreateFromGlobals()
    {
        // Paypal\Ipn\Message::getRawPost returns the content of php://input.
        // When in the test environment php://input is empty (as we are not performing a post request).
        // To get around this we should mock this method to return a value.
        $ipnMessage = $this->getMockClass(
            'PayPal\Ipn\Message',
            array('getRawPost')
        );

        $ipnMessage::staticExpects($this->any())
            ->method('getRawPost')
            ->will($this->returnValue('param1=value1&param2=value2'));

        $message = $ipnMessage::createFromGlobals();

        $this->assertInstanceOf('PayPal\Ipn\Message', $message);
        $this->assertEquals('value2', $message['param2']);
    }
}
