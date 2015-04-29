<?php

require '../../vendor/autoload.php';

use Mdb\PayPal\Ipn\Event\MessageInvalidEvent;
use Mdb\PayPal\Ipn\Event\MessageVerificationFailureEvent;
use Mdb\PayPal\Ipn\Event\MessageVerifiedEvent;
use Mdb\PayPal\Ipn\ListenerBuilder\Guzzle\ArrayListenerBuilder as ListenerBuilder;

$listenerBuilder = new ListenerBuilder();

// make sure this is actually the data you recieved from PayPal...
$data = array(
    'foo' => 'bar',
    'bar' => 'baz',
);

$listenerBuilder->setData($data);

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
