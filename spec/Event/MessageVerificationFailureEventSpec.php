<?php

namespace spec\Mdb\PayPal\Ipn\Event;

use Mdb\PayPal\Ipn\Message;
use PhpSpec\ObjectBehavior;

class MessageVerificationFailureEventSpec extends ObjectBehavior
{
    public function let(Message $message)
    {
        $this->beConstructedWith($message, 'foo');
    }

    public function it_should_be_an_event()
    {
        $this->shouldHaveType('Symfony\Contracts\EventDispatcher\Event');
        $this->shouldHaveType('Mdb\PayPal\Ipn\Event\MessageVerificationEvent');
    }

    public function it_should_retrieve_an_ipn_message(Message $message)
    {
        $this->getMessage()->shouldReturn($message);
    }

    public function it_should_retrieve_an_error_message()
    {
        $this->getError()->shouldReturn('foo');
    }
}
