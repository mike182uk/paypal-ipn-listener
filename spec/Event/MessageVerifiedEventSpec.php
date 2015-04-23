<?php

namespace spec\Mdb\PayPal\Ipn\Event;

use Mdb\PayPal\Ipn\Message;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class MessageVerifiedEventSpec extends ObjectBehavior
{
    function let(Message $message)
    {
        $this->beConstructedWith($message);
    }

    function it_should_be_an_event()
    {
        $this->shouldHaveType('Symfony\Component\EventDispatcher\Event');
        $this->shouldHaveType('Mdb\PayPal\Ipn\Event\MessageVerificationEvent');
    }

    function it_should_retrieve_the_ipn_message(Message $message)
    {
        $this->getMessage()->shouldReturn($message);
    }
}
