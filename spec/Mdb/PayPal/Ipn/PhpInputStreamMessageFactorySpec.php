<?php

namespace spec\Mdb\PayPal\Ipn;

use Mdb\PayPal\Ipn\Message;
use Mdb\PayPal\Ipn\PhpInputStreamAdapter;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class PhpInputStreamMessageFactorySpec extends ObjectBehavior
{
    function let(
       PhpInputStreamAdapter $phpInputStreamAdapter
    )
    {
        $this->beConstructedWith($phpInputStreamAdapter);
    }

    function it_is_a_message_factory()
    {
        $this->shouldHaveType('Mdb\PayPal\Ipn\MessageFactory');
    }

    function it_can_create_a_message_from_the_input_stream(
        PhpInputStreamAdapter $phpInputStreamAdapter
    )
    {
        $streamContents = 'foo=bar&baz=quz';

        $phpInputStreamAdapter->getContents()->willReturn($streamContents);

        $message = $this->createMessage();

        $message->shouldHaveType('Mdb\PayPal\Ipn\Message');
    }

    function it_url_decodes_values_from_the_input_stream(
        PhpInputStreamAdapter $phpInputStreamAdapter
    )
    {
        $streamContents = 'foo=bar&baz=quz+foo+%28bar%29';

        $phpInputStreamAdapter->getContents()->willReturn($streamContents);

        $message = $this->createMessage();

        $message->__toString()->shouldReturn($streamContents);
    }
}
