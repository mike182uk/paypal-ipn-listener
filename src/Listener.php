<?php

namespace Mdb\PayPal\Ipn;

use Mdb\PayPal\Ipn\Event\MessageInvalidEvent;
use Mdb\PayPal\Ipn\Event\MessageVerificationFailureEvent;
use Mdb\PayPal\Ipn\Event\MessageVerifiedEvent;
use Mdb\PayPal\Ipn\Exception\ServiceException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class Listener
{
    const IPN_VERIFIED_EVENT = 'ipn.message.verified';
    const IPN_INVALID_EVENT = 'ipn.message.invalid';
    const IPN_VERIFICATION_FAILURE_EVENT = 'ipn.message.verification_failure';

    /**
     * @var MessageFactory
     */
    private $messageFactory;

    /**
     * @var Verifier
     */
    private $verifier;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @param MessageFactory           $messageFactory
     * @param Verifier                 $verifier
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        MessageFactory $messageFactory,
        Verifier $verifier,
        EventDispatcherInterface $eventDispatcher)
    {
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
                $eventName = self::IPN_VERIFIED_EVENT;
                $event = new MessageVerifiedEvent($message);
            } else {
                $eventName = self::IPN_INVALID_EVENT;
                $event = new MessageInvalidEvent($message);
            }
        } catch (\UnexpectedValueException $e) {
            $eventName = self::IPN_VERIFICATION_FAILURE_EVENT;
            $event = new MessageVerificationFailureEvent($message, $e->getMessage());
        } catch (ServiceException $e) {
            $eventName = self::IPN_VERIFICATION_FAILURE_EVENT;
            $event = new MessageVerificationFailureEvent($message, $e->getMessage());
        }

        $this->eventDispatcher->dispatch($eventName, $event);
    }

    /**
     * @param callable $listener
     */
    public function onVerified($listener)
    {
        $this->eventDispatcher->addListener(self::IPN_VERIFIED_EVENT, $listener);
    }

    /**
     * @param callable $listener
     */
    public function onInvalid($listener)
    {
        $this->eventDispatcher->addListener(self::IPN_INVALID_EVENT, $listener);
    }

    /**
     * @param callable $listener
     */
    public function onVerificationFailure($listener)
    {
        $this->eventDispatcher->addListener(self::IPN_VERIFICATION_FAILURE_EVENT, $listener);
    }
}
