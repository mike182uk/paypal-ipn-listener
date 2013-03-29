#PayPal IPN Listener [![Build Status](https://secure.travis-ci.org/mike182uk/paypal-ipn-listener.png)](http://travis-ci.org/mike182uk/paypal-ipn-listener)

A PayPal IPN (Instant Payment Notification) listener for PHP >=5.3.0. If you are looking for a < 5.3.0 compatible PayPal IPN Listener i highly recommend - [https://github.com/Quixotix/PHP-PayPal-IPN](https://github.com/Quixotix/PHP-PayPal-IPN) (This package is heavily based around this).

###Features

- Flexible, extensible, component based architecture
- Easily switch between sandbox and production mode
- Generate useful reports (request & response)

###Prerequisites

1. PHP >=5.3.0
2. A good understanding of how the PayPal Instant Payment Notification system works. see [https://cms.paypal.com/cms_content/US/en_US/files/developer/IPNGuide.pdf](https://cms.paypal.com/cms_content/US/en_US/files/developer/IPNGuide.pdf)
3. This package can be installed using composer or can be integrated manually. If you are not using an autoloader make sure you include all of the php files in the ``src`` directory.

***Note:*** *All of the code examples in the rest of this document assume you have the required files above included manually or autoloaded.*

###Architecture

This package is built out of a few components that work together:

1. ``Listener`` - Listens for and processes the IPN
2. ``Request`` - Communicates with PayPal
3. ``Response`` - Handles response from PayPal

The request and response components are swappable. If you have a certain way you need to implement the request, or handle the response you can do this by extending the base classes: ``PayPal\Ipn\Request`` and ``PayPal\Ipn\Response``.

``PayPal\Ipn\Request`` is abstract and must be extended.

By default 2 request components are provided:

1. ``PayPal\Ipn\Request\Curl`` - sends the request to PayPal via Curl
2. ``PayPal\Ipn\Request\Socket`` - sends the request to PayPal via sockets (fsock)

1 response component is provided:

1. ``PayPal\Ipn\Response`` - saves the HTTP status and body from the response from PayPal

###Workflow

1. Create an instance of the request component you want to use to communicate with PayPal. If you want to use a custom **repsonse** component, instantiate this first and pass to the request components constructor.
2. Configure any properties required on the request component (set custom request properties etc.)
3. Create an instance of the listener component and pass it the request component in its constructor.
4. Configure any properties required on the listener component (set mode etc.)
5. Get the listener component to verify the IPN by calling the ``verifyIpn()`` method. If the IPN is verified this method will return true, otherwise it will return false. This should be done in a try catch block as the listener or request components may throw exceptions.
6. You can use the method ``getReport()`` to get the details of the request and the response.

```php
$request = new PayPal\Ipn\Request\Curl();

$request->secure(true); //dont need to do this as its done by default, just demonstrating configuring the request component

$listener = new PayPal\Ipn\Listener($request);

$listener->setMode('sandbox');

try {
	$status = $listener->verifyIpn();
}
catch (Exception $e) {
    $error = $e->getMessage();
    $status = false;
}

if ($status) {
	// verified...
}
else {
	// invalid...
	$report = $listener->getReport();
}
```

A report will look like:

```
--------------------------------------------------------------------------------
[29/03/2013 11:30:55] - https://www.sandbox.paypal.com/cgi-bin/webscr
--------------------------------------------------------------------------------

RESPONSE STATUS:
----------------

200

RESPONSE BODY:
--------------

HTTP/1.1 200 OK
Date: Fri, 29 Mar 2013 18:30:54 GMT
Server: Apache
X-Frame-Options: SAMEORIGIN
Set-Cookie: c9MWDuvPtT9GIMyPc3jwol1VSlO=KbVIUQRbqU9DF1_YFipOUOd5a832twJ54x4IHGQtD2cTQowuLRbR5rESIUNaFUiBLihRwW93-qALGl4neGpS9168qgX-Zsluj6TdeSEzLcxr9ovZ-RqlQ4rZDoe8xHVrPpSkF0%7c22hdUyjFPlSIxGOw4iFkWRKD1jIzUgoyTxXvFE1ZA7oUi8K-HNJ9-YYP99TXZxo_6l-xmm%7c3CJlR-97Ev4_99B9-k1RQeLIlne0QDovW2u5r0Oy2H08QUsZR5LOhVQ6Zgf6c0Wtzk7T5m%7c1364581855; domain=.paypal.com; path=/; Secure; HttpOnly
Set-Cookie: cookie_check=yes; expires=Mon, 27-Mar-2023 18:30:55 GMT; domain=.paypal.com; path=/; Secure; HttpOnly
Set-Cookie: navcmd=_notify-validate; domain=.paypal.com; path=/; Secure; HttpOnly
Set-Cookie: navlns=0.0; expires=Thu, 24-Mar-2033 18:30:55 GMT; domain=.paypal.com; path=/; Secure; HttpOnly
Set-Cookie: Apache=10.72.109.11.1364581854884362; path=/; expires=Sun, 22-Mar-43 18:30:54 GMT
X-Cnection: close
Transfer-Encoding: chunked
Content-Type: text/html; charset=UTF-8

VERIFIED

RAW POST:
---------

cmd=_notify-validate&address_state=CA&quantity=1&txn_id=412577516&last_name=Smith&mc_currency=USD&payer_status=verified&address_status=confirmed&tax=2.02&invoice=abc1234&shipping=3.04&address_street=123%2C+any+street&payer_email=buyer%40paypalsandbox.com&mc_gross1=9.34&item_name=something&first_name=John&business=seller%40paypalsandbox.com&verify_sign=Amg6IbBhoWJKr8kse4uOHb9jn02XA-ysmE.No2VnDuMQSdHCNtd7vYj9&payer_id=TESTBUYERID01&payment_date=11%3A30%3A05+29+Mar+2013+PDT&address_country=United+States&payment_status=Completed&receiver_email=seller%40paypalsandbox.com&payment_type=instant&address_zip=95131&address_city=San+Jose&mc_gross=12.34&mc_fee=0.44&residence_country=US&address_country_code=US¬ify_version=2.1&receiver_id=seller%40paypalsandbox.com&txn_type=web_accept&custom=xyz123&item_number=AK-1234&address_name=John+Smith&test_ipn=1

POST VARIABLES:
---------------

address_state = CA
quantity = 1
txn_id = 412577516
last_name = Smith
mc_currency = USD
payer_status = verified
address_status = confirmed
tax = 2.02
invoice = abc1234
shipping = 3.04
address_street = 123, any street
payer_email = buyer@paypalsandbox.com
mc_gross1 = 9.34
item_name = something
first_name = John
business = seller@paypalsandbox.com
verify_sign = Amg6IbBhoWJKr8kse4uOHb9jn02XA-ysmE.No2VnDuMQSdHCNtd7vYj9
payer_id = TESTBUYERID01
payment_date = 11:30:05 29 Mar 2013 PDT
address_country = United States
payment_status = Completed
receiver_email = seller@paypalsandbox.com
payment_type = instant
address_zip = 95131
address_city = San Jose
mc_gross = 12.34
mc_fee = 0.44
residence_country = US
address_country_code = US
notify_version = 2.1
receiver_id = seller@paypalsandbox.com
txn_type = web_accept
custom = xyz123
item_number = AK-1234
address_name = John Smith
test_ipn = 1
```
You can switch between sandbox or production mode. You do this by calling ``setMode($mode)`` on the listener component. Valid values for ``$mode`` are ``sandbox`` or ``production``. This will set where the request is made too (PayPals sandbox server or production server). Internally this calls the ``setHost()`` method of the request component.

By default the mode is set to ``production`` (this is done in the listener / request component constructor).

###Creating Custom Request Components

To create a custom request component you **must** extend ``PayPal\Ipn\Request`` as this has the base methods and properties that the listener component is dependent on.  There is only 1 abstract method that needs to be implemented: ``send()``. This is the method that makes the request to PayPal.

```php
namespace PayPal\Ipn\Request;

use PayPal\Ipn\Request as IpnRequest;

class CustomRequest extends IpnRequest
{
	public function send()
	{
		//custom communication logic
	}
}
```

###Creating Custom Response Components

To create a custom response component you **should** extend ``PayPal\Ipn\Response`` as this has the base methods and properties that the request component is dependent on.  There are no abstract methods that need to be implemented, but any custom setters for for the ``statusCode`` or ``body`` must set the respective protected properties.

```php
namespace PayPal\Ipn\Response;

use PayPal\Ipn\Response as IpnResponse;

class CustomResponse extends IpnResponse
{
	public function setBody($body)
	{
		$this->body = $body;

		//do something else
	}

	public function setStatusCode($statusCode)
	{
		$this->statusCode = $statusCode;

		//do something else
	}
}
```

###Using Custom Components

#####Request

Using your custom request component is as simple as

1. create an instance of the component
2. configure the component
3. pass to the constructor of the listener component

```php
$request = new PayPal\Ipn\Request\CustomRequest();

$request->someCustomMethod();

$listener = new PayPal\Ipn\Listener($request);

...
```

#####Response

Using your custom response component is as simple as

1. create an instance of the component
2. configure the component
3. pass to the constructor of the request component

```php
$response = new PayPal\Ipn\Response\CustomResponse();

$response->someCustomMethod();

$request = new PayPal\Ipn\Request\CustomRequest(false, $response);

$listener = new PayPal\Ipn\Listener($request);

...
```

***Note:*** *The request component constructor accepts 2 parameters: custom set of data and custom response object. For the request component to just use the data in the ``$_POST`` array pass false (if passing a custom response). See notes below.*

###Notes

#####Data Source

By default the data in the ``$_POST`` array will be used to verify the IPN. In some situations you may not have access to ``$_POST`` (some frameworks unset this and use custom accessors). To get around this you can pass an array of data to the constructor of the request component:

```php
$data = array(
	//...
);

$request = new PayPal\Ipn\Request\Curl($data);

...
```

#####Testing

PayPal provide an Instant Payment Notification (IPN) simulator here: [https://developer.paypal.com/webapps/developer/applications/ipn_simulator](https://developer.paypal.com/webapps/developer/applications/ipn_simulator)

The simulator only tells you if the IPN was sent successfully. To get more information about the status of the IPN (what data was sent, what response it got etc.) you need to record this somewhere (use the listener components ``getStatus()`` method and write to file somewhere etc).

