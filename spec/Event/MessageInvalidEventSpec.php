<?php

namespace spec\Mdb\PayPal\Ipn\Event;

use Mdb\PayPal\Ipn\Message;
use PhpSpec\ObjectBehavior;

class MessageInvalidEventSpec extends ObjectBehavior
{
    public function let(Message $message)
    {
        $this->beConstructedWith($message);
    }

    public function it_should_be_an_event()
    {
        $this->shouldHaveType('Symfony\Contracts\EventDispatcher\Event');
        $this->shouldHaveType('Mdb\PayPal\Ipn\Event\MessageVerificationEvent');
    }

    public function it_should_retrieve_the_ipn_message(Message $message)
    {
        $this->getMessage()->shouldReturn($message);
    }
}
