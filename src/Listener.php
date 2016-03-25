<?php

namespace Mdb\PayPal\Ipn;

use Http\Client\Exception;
use Http\Message\StreamFactory;
use Mdb\PayPal\Ipn\Event\IpnInvalidEvent;
use Mdb\PayPal\Ipn\Event\IpnVerificationFailureEvent;
use Mdb\PayPal\Ipn\Event\IpnVerifiedEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class Listener
{
    const IPN_VERIFIED_EVENT = 'ipn.message.verified';
    const IPN_INVALID_EVENT = 'ipn.message.invalid';
    const IPN_VERIFICATION_FAILURE_EVENT = 'ipn.message.verification_failure';

    /**
     * @var StreamFactory
     */
    private $streamFactory;

    /**
     * @var Verifier
     */
    private $verifier;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @param StreamFactory            $streamFactory
     * @param Verifier                 $verifier
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        StreamFactory  $streamFactory,
        Verifier $verifier,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->streamFactory = $streamFactory;
        $this->verifier = $verifier;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function listen()
    {
        $datas = \GuzzleHttp\Psr7\parse_query($this->createStream()->getContents(), PHP_QUERY_RFC1738);

        try {
            $result = $this->verifier->verify($datas);

            if ($result) {
                $eventName = self::IPN_VERIFIED_EVENT;
                $event = new IpnVerifiedEvent($datas);
            } else {
                $eventName = self::IPN_INVALID_EVENT;
                $event = new IpnInvalidEvent($datas);
            }
        } catch (\UnexpectedValueException $e) {
            $eventName = self::IPN_VERIFICATION_FAILURE_EVENT;
            $event = new IpnVerificationFailureEvent($datas, $e->getMessage());
        } catch (Exception $e) {
            $eventName = self::IPN_VERIFICATION_FAILURE_EVENT;
            $event = new IpnVerificationFailureEvent($datas, $e->getMessage());
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

    /**
     * @return \Psr\Http\Message\StreamInterface
     */
    protected function createStream()
    {
        return $this->streamFactory->createStream(\GuzzleHttp\Psr7\try_fopen('php://input', 'r'));
    }
}
