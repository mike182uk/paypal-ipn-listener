<?php

namespace Mdb\PayPal\Ipn;

use Mdb\PayPal\Ipn\Event\MessageInvalidEvent;
use Mdb\PayPal\Ipn\Event\MessageVerificationFailureEvent;
use Mdb\PayPal\Ipn\Event\MessageVerifiedEvent;
use Mdb\PayPal\Ipn\Exception\ApiRequestFailureException;
use Symfony\Component\EventDispatcher\EventDispatcher;
use UnexpectedValueException;

class Listener
{
    const EVENT_IPN_VERIFIED = 'ipn.message.verified';
    const EVENT_IPN_INVALID  = 'ipn.message.invalid';
    const EVENT_IPN_VERIFICATION_FAILURE = 'ipn.message.verification.failure';

    /**
     * @var MessageFactory
     */
    private $messageFactory;

    /**
     * @var Verifier
     */
    private $verifier;

    /**
     * @var EventDispatcher
     */
    private $eventDispatcher;

    /**
     * @param MessageFactory  $messageFactory
     * @param Verifier        $verifier
     * @param EventDispatcher $eventDispatcher
     */
    public function __construct(
        MessageFactory $messageFactory,
        Verifier $verifier,
        EventDispatcher $eventDispatcher
    ) {
        $this->messageFactory = $messageFactory;
        $this->verifier = $verifier;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function listen()
    {
        $message = $this->messageFactory->createMessage();

        try {
            $result = $this->verifier->verify($message);

            if ($result) {
                $eventName = self::EVENT_IPN_VERIFIED;
                $event = new MessageVerifiedEvent($message);
            } else {
                $eventName = self::EVENT_IPN_INVALID;
                $event = new MessageInvalidEvent($message);
            }
        } catch (UnexpectedValueException $e) {
            $eventName = self::EVENT_IPN_VERIFICATION_FAILURE;
            $event = new MessageVerificationFailureEvent($message);
        } catch (ApiRequestFailureException $e) {
            $eventName = self::EVENT_IPN_VERIFICATION_FAILURE;
            $event = new MessageVerificationFailureEvent($message);
        }

        $this->eventDispatcher->dispatch($eventName, $event);
    }

    /**
     * @param mixed $listener
     */
    public function onVerified($listener)
    {
        $this->eventDispatcher->addListener(self::EVENT_IPN_VERIFIED, $listener);
    }

    /**
     * @param mixed $listener
     */
    public function onInvalid($listener)
    {
        $this->eventDispatcher->addListener(self::EVENT_IPN_INVALID, $listener);
    }

    /**
     * @param mixed $listener
     */
    public function onVerificationFailure($listener)
    {
        $this->eventDispatcher->addListener(self::EVENT_IPN_VERIFICATION_FAILURE, $listener);
    }
}
