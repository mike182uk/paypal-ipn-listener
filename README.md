#PayPal IPN Listener [![Build Status](https://secure.travis-ci.org/mike182uk/paypal-ipn-listener.png)](http://travis-ci.org/mike182uk/paypal-ipn-listener)

A PayPal IPN (Instant Payment Notification) listener for PHP >=5.3.0. If you are looking for a < 5.3.0 compatible PayPal IPN Listener i highly recommend [https://github.com/Quixotix/PHP-PayPal-IPN](https://github.com/Quixotix/PHP-PayPal-IPN).

## Index

- [Features](#features)
- [Prerequisites](#prerequisites)
- [Installation](#installation)
- [Architecture](#architecture)
- [Usage](#usage)
- [The Components](#componenets)
	- [The Listener](#listener)
	- [The Message](#message)
	- [The Verifier](#verifier)
	- [The Verification Response](#vresponse)
- [Notes](#notes)

##<a id="features"></a>Features

- Flexible, extensible, component based architecture
- Easily switch between sandbox and production mode
- Generate status reports (request & response)

##<a id="prerequisites"></a>Prerequisites

1. PHP >=5.3.0
2. A good understanding of how the PayPal Instant Payment Notification system works. See [https://developer.paypal.com/webapps/developer/docs/classic/ipn/integration-guide/IPNIntro/](https://developer.paypal.com/webapps/developer/docs/classic/ipn/integration-guide/IPNIntro/)

##<a id="installation"></a>Installation

### Composer

Add this package as a dependency in `composer.json`.

```js
{
    "require" : {
        "mike182uk/paypal-ipn-listener" : "v2.0.0"
    }
}
```

##<a id="architecutre"></a>Architecutre

This package is made up of several componenets that work together:

- `Listener` - Listens for and processes the IPN Messages
- `Message` - The IPN Message
- `Verifier` - Verifies the IPN message with PayPal
- `VerificationResponse` - The verification response from PayPal

The `Verifier` is what communicates with PayPal. This component is swappable (as you can use different methods of communicating with PayPal). This package includes 2 verifiers:

- `CurlVerifier` - Verifies the IPN message using `curl`
- `SocketVerifier` - Verifies the IPN message using sockets `fsockopen`

##<a id="usage"></a>Usage

###Basic Usage

```php
use PayPal\Ipn\Listener;
use PayPal\Ipn\Message;
use PayPal\Ipn\Verifier\CurlVerifier;
use PayPal\Ipn\VerificationResponse;

$listener = new Listener;
$verifier = new CurlVerifier;
$ipnMessage = Message::createFromGlobals(); // uses php://input

$verifier->setIpnMessage($ipnMessage);
$verifier->setEnvironment('sandbox'); // can either be sandbox or production

$listener->setVerifier($verifier);

$listener->listen(function() use ($listener){
    // on verified IPN (everything is good!)
    $resp = $listener->getVerifier()->getVerificationResponse();


}, function() use ($listener){
    // on invalid IPN (soemthings not right!)
    $report = $listener->getReport();
    $resp = $listener->getVerifier()->getVerificationResponse();

});
```

1. Create an instance of `PayPal\Ipn\Listener`
2. Create an instance of the verifier to used (in the example above we are using `PayPal\Ipn\Verifier\CurlVerifier`)
3. Create an instance of `PayPal\Ipn\Message` using the `createFromGlobals` method (this creates a new instance of `PayPal\Ipn\Message` using the data from `php://input`)
4. Set the IPN message on the verifier
5. Set the required environment for the verifier
6. Set the verifier on the listener
7. Set the listener to listen. Pass 2 closures: 1 for when the IPN is verified and another for when the IPN

##<a id="components"></a>The Components

###<a id="listener"></a>The Listener

`PayPal\Ipn\Listener`

The listener is responsible for "listening" for IPN messages from PayPal. When it receives an IPN message it will try and verify the IPN message with PayPal using a verifier. Different actions can be specificed for the result of the verification (`verified` or `invalid`).

The listener will not work without having a verifier set. The verifier must be an instance of `PayPal\Ipn\Verifier`. If a verifier is not set a `RuntimeException` will be thrown whenever the listener tries to process the IPN message.

The quickest way of listening for an IPN message is using the `listen` method. This takes 2 closures as arguments - One to execute if the IPN message was verified and one to execute if the IPN message was invalid.

```php
$listener->listen(function() use ($listener){
    // on verified IPN


}, function() use ($listener){
    // on invalid IPN

});
```

You can set multiple callbacks per outcome using the `onVerifiedIpn` and `onInvalidIpn` methods. Both of these methods take a closure as a single argument.

```php
$listener->onVerifiedIpn(function(){
    // ...
});


$listener->onInvalidIpn(function(){
    // ...
});
```

You can make the listener process the IPN message manually using the `processIpn` method. This method takes one argument: `$executeCallbacks` which dictates whether the callbacks are executed or not (by default `$executeCallbacks` = `true`). `processIpn` will return `true` if the IPN message is verified and `false` if the IPN message is invalid.

```php
$verificationStatus = $listener->processIpn();
```

Internally, the `listen` method just makes use of the methods detailed above!

At anytime you can get a text based report of latest IPN verification request using the `getReport` method. This will produce a text based report like:


```

```

```php
$report = $listener->getReport();
```

`getReport` can only be called if a valid verifier has been set, otherwise a `RuntimeException` will be thrown.

###<a id="message"></a>The Message

`PayPal\Ipn\Message`

The message encapsulates the IPN data sent by PayPal. PayPal send the IPN message to the listener via a `POST` request. The message is just an object representation of this data.

You can set the data in the message by passing the data as an array to the constructor of the message.

```php
use PayPal\Ipn\Message;

$data = array(
    // ...
);

$message = new Message($data);
```

Alternatively, you can use the `createFromGlobals` static method which returns an instance of `PayPal\Ipn\Message` populated with any data present in `php://input`.

```php
use PayPal\Ipn\Message;

$message = new Message::createFromGlobals();
```

The message implements `ArrayAccess` and `IteratorAggregate`. This means you can access data from the message like an array:

```php
$txn_id = $message['txn_id'];
$item_1_price = $message['item_1_price'];
```

and you can also iterate over the data in the message:

```php
foreach ($message as $k => $v) {
	// ...
}
```

The message can be cast to a string. When casted to a string it becomes a serialized string of data similar to that originally contained in `php://input`.

```php
$messageStr = (string) $message;
```

###<a id="verifier"></a>The Verifier

`PayPal\Ipn\Verifier`

The verifier is responsible for verifing the IPN message with PayPal. The verifier contains the implementation for communicating with PayPal. Different verifiers can be used to communicate with PayPal. This package contains 2 verifiers:

- `PayPal\Ipn\Verifier\CurlVerifier` - Verifies the IPN message using `curl`
- `PayPal\Ipn\Verifier\SocketVerifier` - Verifies the IPN message using sockets `fsockopen`

You can create your own verifier by extending `PayPal\Ipn\Verifier`. All verifiers must extend this class otherwise they will be incompatible with the listener.

The verifier requires an IPN message (`PayPal\Ipn\Message`) to verify with PayPal. If an IPN message has not been set and the verifier is requested to verify the IPN message a `RuntimeException` will be thrown.

You can set and get the verifiers IPN message using `setIpnMessage` and `getIpnMessage`.

```php
use PayPal\Ipn\Verifier\CurlVerifier;
use PayPal\Ipn\Message;

$verifier = new CurlVerifier;
$message = Message::createFromGlobals();

// set IPN message
$verifier->setIpnMessage($message);

// get IPN message
$message = $verifier->getIpnMessage();

```

The IPN message is verified using the `verify` method.

```php
$verificationStatus = $verifier->verify();
```

If verification is successful and the IPN message is verified `verify` returns `true`, otherwise it returns `false`.

A verification request is considered successful if a `200` response is returned by the PayPal server and the body of the response contains the word `VERIFIED` or `INVALID`. If one of these conditions is not met a `UnexpectedValueException` is thrown.

The verifier creates a VerificationResponse object (`PayPal\IPN\VerificationResponse`) internally which stores the details of the response from the PayPal server.

This can be accessed using the `getVerificationResponse` method. The verification response will only be available when a verification request is made. Calling `getVerificationResponse` before the verification request has been made will return `null`.

```php
$verificationStatus = $verifier->verify();

$verificationResponse = $verifier->getVerificationResponse();
```

Before you can use the verifier you must set the environment for the verifier. This will dictate where the verification requests will be sent too (PayPal sandbox servers or PayPal production server). The environment can either be set to `production` or `sandbox`.

```php
$verifier->setEnvironment('sandbox');
```
You can also get the current environment using `getEnvironment`.

```php
$env = $verifier->getEnvironment();
```

You can get the host and URI being used to submit verification requests to using `getHost` and `getRequestUri`.

```php
$host = $verifier->getHost();
$uri = $verifier->getRequestUri();
```

If you want the verification request to be sent over SSL, use the `secure` method. It takes a boolean value: `true` to send over SSL, `false` to not send over SSL.

```php
$verifier->secure(true); // send over SSL

$verifier->secure(false); // do not send over SSL
```
By default verification requests **are** sent over SSL.

The verifier will wait up to 30 seconds for the PayPal server to respond. This can be changed using the `setTimeout` method.

```php
$verifier->setTimeout(60); // set timeout to 60 seconds
```

###<a id="vresponse"></a>The Verification Response

`PayPal\IPN\VerificationResponse`

The verification response encaspulates information about the response received from the PayPal server when a verification request was made. This is usually constructed by the verifier.

The verification response has 2 methods: `getStatusCode` and `getBody`.

```php
$verificationStatus = $verifier->verify();

$verificationResponse = $verifier->getVerificationResponse();

$verificationResponseStatus = $verificationResponse->getStatusCode();
$verificationResponseBody = $verificationResponse->getBody();
```

##<a id="notes"></a>Notes

###Testing

PayPal provide an Instant Payment Notification (IPN) simulator here: [https://developer.paypal.com/webapps/developer/applications/ipn_simulator](https://developer.paypal.com/webapps/developer/applications/ipn_simulator)

The simulator only tells you if the IPN was sent successfully. To get more information about the status of the IPN (what data was sent, what response it got etc.) you can use the `getReport()` method of `PayPal\Ipn\Listener` to generate a status report. You can then save this to a file or email it to yourself.

