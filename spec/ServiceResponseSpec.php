<?php

namespace spec\Mdb\PayPal\Ipn;

use PhpSpec\ObjectBehavior;

class ServiceResponseSpec extends ObjectBehavior
{
    public function let()
    {
        $this->beConstructedWith('foo');
    }

    public function it_should_retrieve_the_body()
    {
        $this->getBody()->shouldReturn('foo');
    }
}
