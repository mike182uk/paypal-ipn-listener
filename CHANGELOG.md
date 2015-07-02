#Changelog

##4.0.0

- Rename `Mdb\PayPal\Ipn\ListenerBuilder\ModeDependentServiceEnpoint` to `Mdb\PayPal\Ipn\ListenerBuilder\ModeDependentServiceEndpoint`

##3.0.0

- Rewrite from the ground up

##2.0.2

- Send `User-Agent` header in `CurlVerifier::sendVerificationRequest`
- Send `Connection: Close` header in `CurlVerifier::sendVerificationRequest`

##2.0.1

- use TLSv1 instead of SSLv3
- add `CurlVerifier::forceSSL`
- deprecate `CurlVerifier::forceSSLv3`
- make calls made to `CurlVerifier::forceSSLv3` resolve to `CurlVerifier::forceSSL`

##2.0.0

- Rewrite from the ground up

##1.1.1

- add MIT license

##1.1.0

- Removed ``PayPal\Ipn\Response\Standard``
- ``PayPal\Ipn\Response`` is no longer abstract
- Renamed ``status`` property on ``PayPal\Ipn\Response`` to ``statusCode``. Getter / setters also renamed
- Renamed ``getStatusReport`` method on ``PayPal\Ipn\Listener`` to ``getReport``
- Added basic listener test
- Misc refactoring + cleaning up

##1.0.0

First release
