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
        $datas = $this->parseQuery($this->createStream()->getContents());

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
        $ex = null;
        set_error_handler(function () use ($filename, $mode, &$ex) {
            $ex = new \RuntimeException(sprintf(
                'Unable to open %s using mode %s: %s',
                $filename,
                $mode,
                func_get_args()[1]
            ));
        });

        $handle = fopen('php://input', 'r');
        restore_error_handler();

        if ($ex) {
            /* @var $ex \RuntimeException */
            throw $ex;
        }

        return $this->streamFactory->createStream($handle);
    }

    private function parseQuery($str)
    {
        $result = [];

        if ($str === '') {
            return $result;
        }

        foreach (explode('&', $str) as $kvp) {
            $parts = explode('=', $kvp, 2);
            $key = urldecode($parts[0]);
            $value = isset($parts[1]) ? urldecode($parts[1]) : null;

            if (!isset($result[$key])) {
                $result[$key] = $value;
            } else {
                if (!is_array($result[$key])) {
                    $result[$key] = [$result[$key]];
                }
                $result[$key][] = $value;
            }
        }

        return $result;
    }
}
