<?php

namespace spec\Mdb\PayPal\Ipn\MessageFactory;

use Mdb\PayPal\Ipn\InputStream;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class InputStreamMessageFactorySpec extends ObjectBehavior
{
    function let(InputStream $inputStream)
    {
        $this->beConstructedWith($inputStream);
    }

    function it_should_be_a_message_factory()
    {
        $this->shouldHaveType('Mdb\PayPal\Ipn\MessageFactory');
    }

    function it_should_create_a_message_from_the_input_stream(InputStream $inputStream)
    {
        $streamContents = 'foo=bar&baz=quz';

        $inputStream->getContents()->willReturn($streamContents);

        $this->createMessage()->shouldHaveType('Mdb\PayPal\Ipn\Message');
    }

    function it_should_url_decode_values_from_the_input_stream(InputStream $inputStream)
    {
        $streamContents = 'foo=bar&baz=quz%2Bfoo%2B%28bar%29';

        $inputStream->getContents()->willReturn($streamContents);

        $message = $this->createMessage();

        $message->__toString()->shouldReturn($streamContents);
    }
}
