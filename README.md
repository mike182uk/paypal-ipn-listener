#PayPal IPN Listener
[![Packagist](https://img.shields.io/packagist/v/mike182uk/paypal-ipn-listener.svg?style=flat-square)](https://packagist.org/packages/mike182uk/paypal-ipn-listener)
[![Build Status](https://img.shields.io/travis/mike182uk/paypal-ipn-listener.svg?style=flat-square)](http://travis-ci.org/mike182uk/paypal-ipn-listener)
[![Scrutinizer Quality Score](https://img.shields.io/scrutinizer/g/mike182uk/paypal-ipn-listener.svg?style=flat-square)](https://scrutinizer-ci.com/g/mike182uk/paypal-ipn-listener/)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/969dd452-b91e-4048-a871-5babcd64b834/mini.png)](https://insight.sensiolabs.com/projects/969dd452-b91e-4048-a871-5babcd64b834)
[![Total Downloads](https://img.shields.io/packagist/dt/mike182uk/paypal-ipn-listener.svg?style=flat-square)](https://packagist.org/packages/mike182uk/paypal-ipn-listener)
[![License](https://img.shields.io/github/license/mike182uk/paypal-ipn-listener.svg?style=flat-square)](https://packagist.org/packages/mike182uk/paypal-ipn-listener)

A PayPal IPN (Instant Payment Notification) listener for PHP >=5.5

## Index

- [Prerequisites](#prerequisites)
- [Installation](#installation)
- [Architecture](#architecture)
- [Usage](#usage)
- [Extending](#extending)
- [Notes](#notes)

##<a id="prerequisites"></a>Prerequisites

1. PHP >=5.5.0
2. A good understanding of how the PayPal Instant Payment Notification system works. See [https://developer.paypal.com/webapps/developer/docs/classic/ipn/integration-guide/IPNIntro/](https://developer.paypal.com/webapps/developer/docs/classic/ipn/integration-guide/IPNIntro/)

##<a id="installation"></a>Installation

### Composer

Add this package as a dependency in `composer.json` using the `composer require` command:

    $ composer require mike182uk/paypal-ipn-listener
    
##<a id="architecture"></a>Architecture

This package is made up of several components that work together:

- `Listener` - Listens for and processes the IPN messages
- `Verifier` - Verifies the IPN message with PayPal
- `Service` - Communicates with PayPal
- `Message` - Wrapper around the IPN message
- `MessageFactory` - Creates a new message instance from a data source
- `EventDispatcher` - Dispatches events

The listener creates a `Message` using a `MessageFactory`. The `Message` is passed to the `Verifier` which uses a `Service` to communicate with PayPal. The `Listener` uses the `EventDispatcher` to dispatch events relating to the outcome of the IPN message verification.

The `MessageFactory` and `Service` components are swappable components.

This package provides 2 message factories:

1. `Mdb\PayPal\Ipn\MessageFactory\InputStreamMessageFactory` - Creates a message from the `php://input` stream
2. `Mdb\PayPal\Ipn\MessageFactory\ArrayMessageFactory` - Creates a message from an array passed to the `setData` method

This package provides 1 service:

1. `Mdb\PayPal\Ipn\Service\GuzzleService` - Uses [Guzzle](https://github.com/guzzle/guzzle) to communicate with PayPal

##<a id="usage"></a>Usage

You can either build up the listener object manually or you can use a listener builder. This package provides 2 listener builders:

1. `Mdb\PayPal\Ipn\ListenerBuilder\Guzzle\InputStreamListenerBuilder` - Builds a listener using the guzzle service and the input stream message factory
2. `Mdb\PayPal\Ipn\ListenerBuilder\Guzzle\ArrayListenerBuilder` - Builds a listener using the guzzle service and the array message factory

Using a listener builder is the prefered way of building up a listener object.

###Using a listener builder

```php
use Mdb\PayPal\Ipn\ListenerBuilder\Guzzle\InputStreamListenerBuilder as ListenerBuilder;

$listener = (new ListenerBuilder)->build();
```

###Building up the listener manually

```php
use GuzzleHttp\Client;
use Mdb\PayPal\Ipn\InputStream;
use Mdb\PayPal\Ipn\Listener;
use Mdb\PayPal\Ipn\MessageFactory\InputStreamMessageFactory;
use Mdb\PayPal\Ipn\Service\GuzzleService;
use Mdb\PayPal\Ipn\Verifier;
use Symfony\Component\EventDispatcher\EventDispatcher;

$service = new GuzzleService(
    new Client(),
    'https://www.sandbox.paypal.com/cgi-bin/webscr'
);

$verifier = new Verifier($service);

$messageFactory = new InputStreamMessageFactory(new InputStream());

$listener = new Listener(
    $messageFactory,
    $verifier,
    new EventDispatcher()
);
```

Alot of plumbing is needed to create the listener manually. The job of the listener builder is to abstract away this logic.

###Subscribing to events

Once you have created the listener object you can subscribe to the events that it will dispatch:

```php
use Mdb\PayPal\Ipn\Event\MessageVerifiedEvent;
use Mdb\PayPal\Ipn\Event\MessageInvalidEvent;
use Mdb\PayPal\Ipn\Event\MessageVerificationFailureEvent;

$listener->onVerified(function (MessageVerifiedEvent $event) {
   $ipnMessage = $event->getMessage();
   
   // IPN message was verified, everything is ok! Do your processing logic here...
});

$listener->onInvalid(function (MessageInvalidEvent $event) {
   $ipnMessage = $event->getMessage();
   
   // IPN message was was invalid, something is not right! Do your logging here...
});

$listener->onVerificationFailure(function (MessageVerificationFailureEvent $event) {
    $error = $event->getError();
    
    // Something bad happend when trying to communicate with PayPal! Do your logging here...
});
```

You can use any [callable](https://php.net/manual/en/language.types.callable.php) when subscribing to an event:

```php
class IpnProcessor
{
    public function onVerified(MessageVerifiedEvent $event)
    {
        $message = $event->getMessage();
        
        // ...
    }
}

$listener->onVerified(array(new Processor, 'onVerified'));
```

```php
class IpnProcessor
{
    public static function onVerified(MessageVerifiedEvent $event)
    {
        $message = $event->getMessage();
        
        // ...
    }
}

$listener->onVerified(array('IpnProcessor', 'onVerified'));
```

###Listening for IPN messages

The last thing you need to do to kick of the process is listen for an IPN message:

```php
$listener->listen();
```

###Full Example 

```php
use Mdb\PayPal\Ipn\Event\MessageVerifiedEvent;
use Mdb\PayPal\Ipn\Event\MessageInvalidEvent;
use Mdb\PayPal\Ipn\Event\MessageVerificationFailureEvent;
use Mdb\PayPal\Ipn\ListenerBuilder\Guzzle\InputStreamListenerBuilder as ListenerBuilder;

$listener = (new ListenerBuilder)->build();

$listener->onVerified(function (MessageVerifiedEvent $event) {
   $ipnMessage = $event->getMessage();
   
   // IPN message was verified, everything is ok! Do your processing logic here...
});

$listener->onInvalid(function (MessageInvalidEvent $event) {
   $ipnMessage = $event->getMessage();
   
   // IPN message was was invalid, something is not right! Do your logging here...
});

$listener->onVerificationFailure(function (MessageVerificationFailureEvent $event) {
    $error = $event->getError();
    
    // Something bad happend when trying to communicate with PayPal! Do your logging here...
});

$listener->listen();
```

####Sandbox mode

When using one of the provided listener builders you can set your listener to use PayPal's sandbox for testing purposes:

```php
$listenerBuilder = new ListenerBuilder();

$listenerBuilder->useSandbox(); // use PayPal sandbox

$listener = $listenerBuilder->build();
```

You can find some full usage examples in the examples directory.

##<a id="extending"></a>Extending

To create your own service you must implement `Mdb\PayPal\Ipn\Service`.

To create your own message factory you must implement `Mdb\PayPal\Ipn\MessageFactory`.

To create your own listener builder it is best to extend `Mdb\PayPal\Ipn\ListenerBuilder` as this provides most of the boilerplate code needed to create a listener builder.

You will notice that when using any of the provided guzzle listener builders that there is a `useSandbox` method exposed. You can add this functionality to your listener builder by using the `Mdb\PayPal\Ipn\ListenerBuilder\ModeDependentServiceEndpoint` trait (see `Mdb\PayPal\Ipn\ListenerBuilder\GuzzleListenerBuilder` for usage example).

##<a id="notes"></a>Notes

###Testing

PayPal provide an Instant Payment Notification (IPN) simulator here: [https://developer.paypal.com/webapps/developer/applications/ipn_simulator](https://developer.paypal.com/webapps/developer/applications/ipn_simulator)
