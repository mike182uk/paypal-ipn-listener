<?php

namespace spec\Mdb\PayPal\Ipn\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Message\ResponseInterface;
use Mdb\PayPal\Ipn\Message;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class GuzzleServiceSpec extends ObjectBehavior
{
    function let(Client $httpClient)
    {
        $this->beConstructedWith($httpClient, 'http://foo.bar');
    }

    function it_should_be_a_service()
    {
        $this->shouldHaveType('Mdb\PayPal\Ipn\Service');
    }

    function it_should_return_a_service_response_when_verifying_an_ipn_message(
        Client $httpClient,
        Message $message,
        ResponseInterface $response
    ) {
        $response->getBody()->willReturn('foo');

        $httpClient->post(
            Argument::type('string'),
            Argument::type('array')
        )->willReturn($response);

        $message->getAll()->willReturn(['foo' => 'bar']);

        $response = $this->verifyIpnMessage($message);

        $response->shouldHaveType('Mdb\PayPal\Ipn\ServiceResponse');
        $response->getBody()->shouldReturn('foo');
    }

    function it_should_throw_a_service_exception_when_a_request_fails(
        Client $httpClient,
        Message $message
    ) {
        $httpClient->post(
            Argument::type('string'),
            Argument::type('array')
        )->willThrow('Exception');

        $message->getAll()->willReturn(['foo' => 'bar']);

        $this->shouldThrow('Mdb\PayPal\Ipn\Exception\ServiceException')->during('verifyIpnMessage', array($message));
    }
}
