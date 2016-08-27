#Changelog

##7.0.1

- Update dependencies so version of guzzle with security vuln is not depended on

##7.0.0

- Use `rawurlencode` / `rawurldecode` in place of `urlencode` / `urldecode` to resolve [this issue](https://github.com/paypal/ipn-code-samples/issues/51) ([@swader](https://github.com/Swader))

##6.0.0

- Upgrade `Guzzle` to `~6.0.0`
- Upgrade `Behat` to `~3.1`
- Use correct vendor for `php-cs-fixer`

##5.0.0

- Drop support for php 5.4 ([@Lctrs](https://github.com/Lctrs))
- Upgrade symfony deps ([@Lctrs](https://github.com/Lctrs))
- Refactor travis config ([@Lctrs](https://github.com/Lctrs))

##4.0.1

- Use `EventDispatcherInterface` type hint instead of `EventDispatcher` in `Mdb\PayPal\Ipn\Listener` ([@mablae](https://github.com/mablae))

##4.0.0

- Rename `Mdb\PayPal\Ipn\ListenerBuilder\ModeDependentServiceEnpoint` to `Mdb\PayPal\Ipn\ListenerBuilder\ModeDependentServiceEndpoint`

##3.0.0

- Rewrite from the ground up

##2.0.2

- Send `User-Agent` header in `CurlVerifier::sendVerificationRequest` ([@stefanneubig](https://github.com/stefanneubig))
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

- Removed `PayPal\Ipn\Response\Standard`
- `PayPal\Ipn\Response` is no longer abstract
- Renamed `status` property on `PayPal\Ipn\Response` to `statusCode`. Getter / setters also renamed
- Renamed `getStatusReport` method on `PayPal\Ipn\Listener` to `getReport`
- Added basic listener test
- Misc refactoring + cleaning up

##1.0.0

First release
