<?php

namespace spec\Mdb\PayPal\Ipn\Event;

use Mdb\PayPal\Ipn\Message;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class MessageVerificationFailureEventSpec extends ObjectBehavior
{
    function let(Message $message)
    {
        $this->beConstructedWith($message, 'foo');
    }

    function it_should_be_an_event()
    {
        $this->shouldHaveType('Symfony\Component\EventDispatcher\Event');
        $this->shouldHaveType('Mdb\PayPal\Ipn\Event\MessageVerificationEvent');
    }

    function it_should_retrieve_an_ipn_message(Message $message)
    {
        $this->getMessage()->shouldReturn($message);
    }

    function it_should_retrieve_an_error_message()
    {
        $this->getError()->shouldReturn('foo');
    }
}
