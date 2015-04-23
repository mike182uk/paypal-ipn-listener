<?php

namespace spec\Mdb\PayPal\Ipn;

use Mdb\PayPal\Ipn\Event\MessageInvalidEvent;
use Mdb\PayPal\Ipn\Event\MessageVerificationFailureEvent;
use Mdb\PayPal\Ipn\Event\MessageVerifiedEvent;
use Mdb\PayPal\Ipn\Listener;
use Mdb\PayPal\Ipn\Message;
use Mdb\PayPal\Ipn\MessageFactory;
use Mdb\PayPal\Ipn\Verifier;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\EventDispatcher\EventDispatcher;

class ListenerSpec extends ObjectBehavior
{
    function let(
        MessageFactory $messageFactory,
        Message $message,
        Verifier $verifier,
        EventDispatcher $eventDispatcher
    ) {
        $messageFactory->createMessage()->willReturn($message);

        $this->beConstructedWith(
            $messageFactory,
            $verifier,
            $eventDispatcher
        );
    }

    function it_should_dispatch_an_event_when_a_message_is_verified(
        Verifier $verifier,
        EventDispatcher $eventDispatcher
    ) {
        $verifier->verify(
            Argument::type('Mdb\PayPal\Ipn\Message')
        )->willReturn(true);

        $eventDispatcher->dispatch(
            Listener::IPN_VERIFIED_EVENT,
            Argument::type('Mdb\PayPal\Ipn\Event\MessageVerifiedEvent')
        )->shouldBeCalled();

        $this->listen();
    }

    function it_should_dispatch_an_event_when_a_message_is_invalid(
        Verifier $verifier,
        EventDispatcher $eventDispatcher
    ) {
        $verifier->verify(
            Argument::type('Mdb\PayPal\Ipn\Message')
        )->willReturn(false);

        $eventDispatcher->dispatch(
            Listener::IPN_INVALID_EVENT,
            Argument::type('Mdb\PayPal\Ipn\Event\MessageInvalidEvent')
        )->shouldBeCalled();

        $this->listen();
    }

    function it_should_dispatch_an_event_when_it_fails_to_verify_a_message_due_to_an_unexpected_response(
        Verifier $verifier,
        EventDispatcher $eventDispatcher
    ) {
        $verifier->verify(
            Argument::type('Mdb\PayPal\Ipn\Message')
        )->willThrow('UnexpectedValueException');

        $eventDispatcher->dispatch(
            Listener::IPN_VERIFICATION_FAILURE_EVENT,
            Argument::type('Mdb\PayPal\Ipn\Event\MessageVerificationFailureEvent')
        )->shouldBeCalled();

        $this->listen();
    }

    function it_should_dispatch_an_event_when_it_fails_to_verify_a_message_due_to_a_service_failure(
        Verifier $verifier,
        EventDispatcher $eventDispatcher
    ) {
        $verifier->verify(
            Argument::type('Mdb\PayPal\Ipn\Message')
        )->willThrow('Mdb\PayPal\Ipn\Exception\ServiceException');

        $eventDispatcher->dispatch(
            Listener::IPN_VERIFICATION_FAILURE_EVENT,
            Argument::type('Mdb\PayPal\Ipn\Event\MessageVerificationFailureEvent')
        )->shouldBeCalled();

        $this->listen();
    }

    function it_should_attach_a_listener_for_the_message_verified_event(EventDispatcher $eventDispatcher)
    {
        $eventDispatcher->addListener(
            Listener::IPN_VERIFIED_EVENT,
            Argument::type('callable')
        )->shouldBeCalled();

        $this->onVerified(function (MessageVerifiedEvent $event) {});
    }

    function it_should_attach_a_listener_for_the_message_invalid_event(EventDispatcher $eventDispatcher)
    {
        $eventDispatcher->addListener(
            Listener::IPN_INVALID_EVENT,
            Argument::type('callable')
        )->shouldBeCalled();

        $this->onInvalid(function (MessageInvalidEvent $event) {});
    }

    function it_should_attach_a_listener_for_the_message_verification_failure_event(EventDispatcher $eventDispatcher)
    {
        $eventDispatcher->addListener(
            Listener::IPN_VERIFICATION_FAILURE_EVENT,
            Argument::type('callable')
        )->shouldBeCalled();

        $this->onVerificationFailure(function (MessageVerificationFailureEvent $event) {});
    }
}
