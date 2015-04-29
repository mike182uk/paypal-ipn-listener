<?php

require '../../vendor/autoload.php';

use Mdb\PayPal\Ipn\Event\MessageInvalidEvent;
use Mdb\PayPal\Ipn\Event\MessageVerificationFailureEvent;
use Mdb\PayPal\Ipn\Event\MessageVerifiedEvent;
use Mdb\PayPal\Ipn\ListenerBuilder\Guzzle\InputStreamListenerBuilder as ListenerBuilder;

$listenerBuilder = new ListenerBuilder();
$listenerBuilder->useSandbox();

$listener = $listenerBuilder->build();

$listener->onInvalid(function (MessageInvalidEvent $event) {
    $ipnMessage = $event->getMessage();

    file_put_contents('outcome.txt', "INVALID\n\n$ipnMessage");
});

$listener->onVerified(function (MessageVerifiedEvent $event) {
    $ipnMessage = $event->getMessage();

    file_put_contents('outcome.txt', "VERIFIED\n\n$ipnMessage");
});

$listener->onVerificationFailure(function (MessageVerificationFailureEvent $event) {
    $error = $event->getError();

    file_put_contents('outcome.txt', "VERIFICATION FAILURE\n\n$error");
});

$listener->listen();
