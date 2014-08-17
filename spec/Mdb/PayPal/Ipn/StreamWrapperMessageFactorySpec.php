<?php

namespace spec\Mdb\PayPal\Ipn;

use Mdb\PayPal\Ipn\Message;
use Mdb\PayPal\Ipn\StreamWrapperAdapter;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class StreamWrapperMessageFactorySpec extends ObjectBehavior
{
    function let(
       StreamWrapperAdapter $streamWrapperAdapter
    )
    {
        $this->beConstructedWith($streamWrapperAdapter);
    }

    function it_is_a_message_factory()
    {
        $this->shouldHaveType('Mdb\PayPal\Ipn\MessageFactory');
    }

    function it_can_create_a_message_from_the_input_stream(
        StreamWrapperAdapter $streamWrapperAdapter
    )
    {
        $streamContents = 'foo=bar&baz=quz';

        $streamWrapperAdapter->getInputStreamContents()->willReturn($streamContents);

        $message = $this->createMessage();

        $message->shouldHaveType('Mdb\PayPal\Ipn\Message');
    }

    function it_url_decodes_values_from_the_input_stream(
        StreamWrapperAdapter $streamWrapperAdapter
    )
    {
        $streamContents = 'foo=bar&baz=quz+foo+%28bar%29';

        $streamWrapperAdapter->getInputStreamContents()->willReturn($streamContents);

        $message = $this->createMessage();

        $message->__toString()->shouldReturn($streamContents);
    }
}
