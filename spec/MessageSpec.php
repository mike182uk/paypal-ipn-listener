<?php

namespace spec\Mdb\PayPal\Ipn;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class MessageSpec extends ObjectBehavior
{
    function let()
    {
        $data = array(
            'foo' => 'bar',
            'baz' => 'quz'
        );

        $this->beConstructedWith($data);
    }

    function it_should_retrieve_a_property()
    {
        $this->get('foo')->shouldReturn('bar');
        $this->get('baz')->shouldReturn('quz');
    }

    function it_should_retrieve_all_properties()
    {
        $this->getAll()->shouldReturn([
            'foo' => 'bar',
            'baz' => 'quz'
        ]);
    }

    function it_should_return_an_empty_string_when_retrieving_a_non_existent_property()
    {
        $this->get('bar')->shouldReturn('');
    }

    function it_can_be_represented_as_a_string()
    {
        $this->__toString()->shouldReturn('foo=bar&baz=quz');
    }

    function it_should_url_encode_property_values_when_represented_as_a_string()
    {
        $data = array(
            'foo' => 'foo + bar (baz)'
        );

        $this->beConstructedWith($data);

        $this->__toString()->shouldReturn('foo=foo%20%2B%20bar%20%28baz%29');
    }

    function it_should_accept_a_string_of_raw_post_data_for_its_data_source()
    {
        $data = 'foo=bar&baz=quz';

        $this->beConstructedWith($data);

        $this->get('foo')->shouldReturn('bar');
        $this->get('baz')->shouldReturn('quz');
    }

    function it_should_url_decode_values_when_using_a_string_of_raw_post_data_for_its_data_source()
    {
        $data = 'foo=foo+%2B+bar+%28baz%29';

        $this->beConstructedWith($data);

        $this->get('foo')->shouldReturn('foo+++bar+(baz)');
    }
}
