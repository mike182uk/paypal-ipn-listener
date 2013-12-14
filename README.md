#PayPal IPN Listener [![Build Status](https://secure.travis-ci.org/mike182uk/paypal-ipn-listener.png)](http://travis-ci.org/mike182uk/paypal-ipn-listener)

A PayPal IPN (Instant Payment Notification) listener for PHP >=5.3.0. If you are looking for a < 5.3.0 compatible PayPal IPN Listener i highly recommend - [https://github.com/Quixotix/PHP-PayPal-IPN](https://github.com/Quixotix/PHP-PayPal-IPN) (this package is based around this).

##Features

- Flexible, extensible, component based architecture
- Easily switch between sandbox and production mode
- Generate status reports (request & response)

##Prerequisites

1. PHP >=5.3.0
2. A good understanding of how the PayPal Instant Payment Notification system works. see [https://developer.paypal.com/webapps/developer/docs/classic/ipn/integration-guide/IPNIntro/](https://developer.paypal.com/webapps/developer/docs/classic/ipn/integration-guide/IPNIntro/)

##Installation

### Composer

Add this package as a dependency in your `composer.json`.

```js
{
    "require" : {
        "mike182uk/paypal-ipn-listener" : "v2.0.0"
    }
}
```

##Usage

```php
use PayPal\Ipn\Listener;
use PayPal\Ipn\Message;
use PayPal\Ipn\Verifier\CurlVerifier;
use PayPal\Ipn\VerificationResponse;

$listener = new Listener;
$verifier = new CurlVerifier;
$ipnMessage = Message::createFromGlobals();

$verifier->setIpnMessage($ipnMessage);
$verifier->setEnvironment('sandbox');

$listener->setVerifier($verifier);

$listener->listen(function() use ($listener){
    // on verified IPN
    $resp = $listener->getVerifier()->getVerificationResponse();


}, function() use ($listener){
    // on invalid IPN
    $report = $listener->getReport();
    $resp = $listener->getVerifier()->getVerificationResponse();

});
```

##Notes

###Testing

PayPal provide an Instant Payment Notification (IPN) simulator here: [https://developer.paypal.com/webapps/developer/applications/ipn_simulator](https://developer.paypal.com/webapps/developer/applications/ipn_simulator)

The simulator only tells you if the IPN was sent successfully. To get more information about the status of the IPN (what data was sent, what response it got etc.) you can use the `getReport()` method of `PayPal\Ipn\Listener` to generate a status report. You can then save this to a file or email it to yourself.

