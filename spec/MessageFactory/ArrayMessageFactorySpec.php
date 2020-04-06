<?php

namespace spec\Mdb\PayPal\Ipn\MessageFactory;

use PhpSpec\ObjectBehavior;

class ArrayMessageFactorySpec extends ObjectBehavior
{
    public function let()
    {
        $this->beConstructedWith(['foo' => 'bar']);
    }

    public function it_should_be_a_message_factory()
    {
        $this->shouldHaveType('Mdb\PayPal\Ipn\MessageFactory');
    }

    public function it_should_create_a_message_from_an_array()
    {
        $this->createMessage()->shouldHaveType('Mdb\PayPal\Ipn\Message');
    }
}
