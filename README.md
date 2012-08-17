#PayPal IPN Listener

A composer compatible PayPal instant payment notification listener for PHP >=5.3.0. If you are looking a PHP 5.x compatible PayPal IPN Listener i highly recommend - [https://github.com/Quixotix/PHP-PayPal-IPN](https://github.com/Quixotix/PHP-PayPal-IPN) (This package is heavily based around this).

###Features

- Flexible, extensible, component based architecture
- Easily switch between sandbox and production mode
- Generate useful status reports (request & response)
- Namespaced, composer ready, framework independent, PSR-0

###Prerequisites

1. PHP>=5.3.0
2. A good understanding of how the PayPal Instant Payment Notification system works. see [https://cms.paypal.com/cms_content/US/en_US/files/developer/IPNGuide.pdf](https://cms.paypal.com/cms_content/US/en_US/files/developer/IPNGuide.pdf)
3. This package can be installed using composer or can be integrated manually. If you are not using an autoloader make sure you include all of the php files in the ``src`` directory.


```php
require '<path-to-src>/src/PayPal/Ipn/Response.php';
require '<path-to-src>/PayPal/Ipn/Response/Standard.php';
require '<path-to-src>/PayPal/Ipn/Request.php';
require '<path-to-src>/PayPal/Ipn/Request/cURL.php';
require '<path-to-src>/PayPal/Ipn/Request/Socket.php';
require '<path-to-src>/PayPal/Ipn/Listener.php';

```
***Note:*** *All of the code examples in the rest of this document assume you have the above files above included manually or autoloaded*

###Architecture

This package is built out of a few components that work together:

1. Listener - Listens for and processes the IPN
2. Request - Communicates with PayPal
3. Response - Stores the response from PayPal

The request and response components are swapable. If you have a certain way you need to implement the request, or store the response you can do this by extending the base classes: ``\PayPal\Ipn\Request`` and ``\PayPal\Ipn\Response``.

Both of these classes are abstract and must be extended.

By default 2 request components are provided:

1. ``\PayPal\Ipn\Request\cURL`` - sends the request to PayPal via cURL
2. ``\PayPal\Ipn\Request\Socket`` - sends the request to PayPal via sockets (fsock)

And 1 response component is provided:

1. ``\PayPal\Ipn\Response\Standard`` - saves the HTTP status and body from the response from PayPal

###Workflow

1. Create an instance of the request component you want to use to communicate with PayPal. If you want to use a custom repsonse component, instantiate this first and pass to the request components constructor.
2. Configure any properties required on the request component (set custom request properties etc.)
3. Create an instance of the listener component and pass it the request component in its constructor.
4. Configure any properties required on the listener component (set mode etc.)
5. Get the listener component to verify the IPN by calling the ``verifyIpn()`` method. If the IPN is verified this method will return true, otherwise it will return false. This should be done in a try catch block as the listener or request components may throw exceptions.
6. You can use the method ``getStatusReport()`` to get the details of the request and the response.

```php
<?php

$request = new \PayPal\Ipn\Request\cURL(); 

$request->useSSL(true); //dont need to do this as its done by default, just demonstrating configuring the request component

$listener = new \PayPal\Ipn\Listener($request);

$listener->setMode('sandbox');

try {
	$status = $listener->verifyIpn();	
}
catch (\Exception $e) {
	echo $e->getMessage();	
}

if ($status) {
	//verified...
}
else {
	//invalid...
	$report = $listener->getStatusReport();
}

```

A standard status report will look like:

```
--------------------------------------------------------------------------------
[16/08/2012 12:45:18] - https://www.sandbox.paypal.com/cgi-bin/webscr
--------------------------------------------------------------------------------

RESPONSE STATUS: 
----------------

200

RESPONSE BODY: 
--------------

HTTP/1.1 200 OK
Date: Thu, 16 Aug 2012 19:45:17 GMT
Server: Apache
X-Frame-Options: SAMEORIGIN
Set-Cookie: c9MWDuvPtT9GIMyPc3jwol1VSlO=Kv1AEAL_1vbz-6Bw3LKSY-DU4rQCAaOoKw1RGJ07QLFG176CoS9-MoJHVtlhOTIKXJcsmoQeMS-UKYhSLHt7j1E5kIz_2vzZ2GnO3EqqfFE63Ju69evuBm6GLrctc-KqfE0L_m%7cE2pk1y69udVsPEIsxe1d4WlL5gwDJiDG40vIjdGe02GsDllp03GTTDL0FesOJmJmF4X2Bm%7c_VxjNUgUVv3NO4zQCdmDPwS5MhStpENsZ0pDvxPKqgj1bN1klsJA8rCVP8PhTGzqdtGmeG%7c1345146318; domain=.paypal.com; path=/; Secure; HttpOnly
Set-Cookie: cookie_check=yes; expires=Sun, 14-Aug-2022 19:45:18 GMT; domain=.paypal.com; path=/; Secure; HttpOnly
Set-Cookie: navcmd=_notify-validate; domain=.paypal.com; path=/; Secure; HttpOnly
Set-Cookie: navlns=0.0; expires=Wed, 11-Aug-2032 19:45:18 GMT; domain=.paypal.com; path=/; Secure; HttpOnly
Set-Cookie: Apache=10.72.109.11.1345146317856113; path=/; expires=Sat, 09-Aug-42 19:45:17 GMT
X-Cnection: close
Transfer-Encoding: chunked
Content-Type: text/html; charset=UTF-8

VERIFIED

POST: 
-----

cmd=_notify-validate&test_ipn=1&payment_type=instant&payment_date=12%3A44%3A16+Aug+16%2C+2012+PDT&payment_status=Completed&address_status=confirmed&payer_status=verified&first_name=John&last_name=Smith&payer_email=buyer%40paypalsandbox.com&payer_id=TESTBUYERID01&address_name=John+Smith&address_country=United+States&address_country_code=US&address_zip=95131&address_state=CA&address_city=San+Jose&address_street=123%2C+any+street&business=seller%40paypalsandbox.com&receiver_email=seller%40paypalsandbox.com&receiver_id=TESTSELLERID1&residence_country=US&item_name=something&item_number=AK-1234&quantity=1&shipping=3.04&tax=2.02&mc_currency=USD&mc_fee=0.44&mc_gross=12.34&mc_gross_1=9.34&txn_type=web_accept&txn_id=168161944&notify_version=2.1&custom=xyz123&charset=windows-1252&verify_sign=An5ns1Kso7MWUdW4ErQKJJJ4qi4-AiDQdSQlWgandPafaHfLyF8oqvxy

USER POST VARS: 
---------------

test_ipn = 1
payment_type = instant
payment_date = 12:44:16 Aug 16, 2012 PDT
payment_status = Completed
address_status = confirmed
payer_status = verified
first_name = John
last_name = Smith
payer_email = buyer@paypalsandbox.com
payer_id = TESTBUYERID01
address_name = John Smith
address_country = United States
address_country_code = US
address_zip = 95131
address_state = CA
address_city = San Jose
address_street = 123, any street
business = seller@paypalsandbox.com
receiver_email = seller@paypalsandbox.com
receiver_id = TESTSELLERID1
residence_country = US
item_name = something
item_number = AK-1234
quantity = 1
shipping = 3.04
tax = 2.02
mc_currency = USD
mc_fee = 0.44
mc_gross = 12.34
mc_gross_1 = 9.34
txn_type = web_accept
txn_id = 168161944
notify_version = 2.1
custom = xyz123
charset = windows-1252
verify_sign = An5ns1Kso7MWUdW4ErQKJJJ4qi4-AiDQdSQlWgandPafaHfLyF8oqvxy
```
You can switch between sandbox or production mode. You do this by calling ``setMode($mode)`` on the listener component. Valid values for $mode are ``sandbox`` or ``production``. This will set where the request is made too (PayPals sandbox server or production server). Internally this calls the ``setHost()`` method of the request component.

By default the mode is set to ``production`` (this is done in the listener / request constructor).

###Creating Custom Request Components

To create a custom request component you **must** extend ``\PayPal\Ipn\Request`` as this has the base methods and properties that the listener component is dependent on.  There is only 1 abstract method that needs to be implemented: ``send()``. This is the method that makes the request to PayPal.

```php
<?php namespace PayPal\Ipn\Request;

class CustomRequest extends \PayPal\Ipn\Request
{
	public function send()
	{
		//custom communication logic
	}
}


```

###Creating Custom Response Components

To create a custom response component you **must** extend ``\PayPal\Ipn\Response`` as this has the base methods and properties that the request component is dependent on.  There are no abstract methods that need to be implemented, but any custom setters for for the ``status`` or ``body`` must set the respective protected properties. 

```php
<?php namespace PayPal\Ipn\Response;

class CustomResponse extends \PayPal\Ipn\Response
{
	public function setBody($body)
	{
		$this->body = $body;
		
		//do something else
	}
	
	public function setStatus($status)
	{
		$this->status = $status;
		
		//do something else
	}
}

```

###Using Custom Components

#####Request

Using your custom request componenet is as simple as 

1. create an instance of the component
2. configure the component
3. pass to the constructor of the listener component

```php
<?php

$request = new \PayPal\Ipn\Request\CustomRequest(); 

$request->someCustomMethod();

$listener = new \PayPal\Ipn\Listener($request);

...

```

#####Response

Using your custom response componenet is as simple as 

1. create an instance of the component
2. configure the component
3. pass to the constructor of the request component

```php
<?php

$response = new \PayPal\Ipn\Response\CustomResponse();

$response->someCustomMethod();

$request = new \PayPal\Ipn\Request\CustomRequest(false, $response); 

$listener = new \PayPal\Ipn\Listener($request);

...

```

***Note:*** *The request component constructor accepts 2 parameters: custom set of data and custom response object. For the request component to just use the data in the ``$_POST`` array pass false (if passing a custom response). See notes below.*

###Notes

#####Data Source

By default the data in the ``$_POST`` array will be used to verify the IPN. In some situations you may not have access to ``$_POST`` (some frameworks unset this and use custom accessors). To get around this you can pass an array of data to the constructor of the request component

```php
<?php

$data = array(
	//...
);

$request = new \PayPal\Ipn\Request\cURL($data);

...

```
