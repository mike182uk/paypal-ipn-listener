<?php

namespace spec\Mdb\PayPal\Ipn;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ServiceResponseSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('foo');
    }

    function it_should_retrieve_the_body()
    {
        $this->getBody()->shouldReturn('foo');
    }
}
