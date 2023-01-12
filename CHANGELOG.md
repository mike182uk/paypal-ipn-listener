# Changelog

## Unreleased

-

## 9.0.4

- allow installation of `symfony/event-dispatcher` v6 - ([#49](https://github.com/mike182uk/paypal-ipn-listener/pull/49) - [@shirshir](https://github.com/shirshir))

## 9.0.3

- fix `http_build_query` `null` deprecation ([#47](https://github.com/mike182uk/paypal-ipn-listener/pull/47) - [@garyross](https://github.com/garyross))

## 9.0.2

- add support for PHP 8 ([#43](https://github.com/mike182uk/paypal-ipn-listener/pull/43) - [@sebdesign](https://github.com/sebdesign))

## 9.0.1

- add support for Guzzle 7 ([#40](https://github.com/mike182uk/paypal-ipn-listener/pull/40), [#41](https://github.com/mike182uk/paypal-ipn-listener/pull/41) - [@sebdesign](https://github.com/sebdesign))

## 9.0.0

- add support for Symfony 5 ([#39](https://github.com/mike182uk/paypal-ipn-listener/pull/39) - [@sebdesign](https://github.com/sebdesign))
- drop support for Symfony <4.3 ([#39](https://github.com/mike182uk/paypal-ipn-listener/pull/39) - [@sebdesign](https://github.com/sebdesign))
- drop support for PHP <7.1 ([#39](https://github.com/mike182uk/paypal-ipn-listener/pull/39) - [@sebdesign](https://github.com/sebdesign))

## 8.0.3

- add support for Symfony 4
- remove bundled `composer.lock` file
- update build config

## 8.0.2

- use standard PHP functions for building / parsing input in `Message` ([#32](https://github.com/mike182uk/paypal-ipn-listener/pull/32) - [@willemstuursma](https://github.com/willemstuursma))

## 8.0.1

- update sandbox endpoint ([#31](https://github.com/mike182uk/paypal-ipn-listener/pull/31) - [@swader](https://github.com/Swader))

## 8.0.0

- drop support for php <5.6
- upgrade dependencies
- remove node.js mock server
- misc refactoring

## 7.0.1

- update dependencies so version of guzzle with security vulnerability is not depended on

## 7.0.0

- use `rawurlencode` / `rawurldecode` in place of `urlencode` / `urldecode` to resolve [this issue](https://github.com/paypal/ipn-code-samples/issues/51) ([@swader](https://github.com/Swader))

## 6.0.0

- upgrade `Guzzle` to `~6.0.0`
- upgrade `Behat` to `~3.1`
- use correct vendor for `php-cs-fixer`

## 5.0.0

- drop support for php 5.4 ([@Lctrs](https://github.com/Lctrs))
- upgrade symfony dependencies ([@Lctrs](https://github.com/Lctrs))
- refactor travis config ([@Lctrs](https://github.com/Lctrs))

## 4.0.1

- use `EventDispatcherInterface` type hint instead of `EventDispatcher` in `Mdb\PayPal\Ipn\Listener` ([@mablae](https://github.com/mablae))

## 4.0.0

- rename `Mdb\PayPal\Ipn\ListenerBuilder\ModeDependentServiceEnpoint` to `Mdb\PayPal\Ipn\ListenerBuilder\ModeDependentServiceEndpoint`

## 3.0.0

- rewrite

## 2.0.2

- send `User-Agent` header in `CurlVerifier::sendVerificationRequest` ([@stefanneubig](https://github.com/stefanneubig))
- send `Connection: Close` header in `CurlVerifier::sendVerificationRequest`

## 2.0.1

- use TLSv1 instead of SSLv3
- add `CurlVerifier::forceSSL`
- deprecate `CurlVerifier::forceSSLv3`
- make calls made to `CurlVerifier::forceSSLv3` resolve to `CurlVerifier::forceSSL`

## 2.0.0

- rewrite

## 1.1.1

- add MIT license

## 1.1.0

- remove `PayPal\Ipn\Response\Standard`
- `PayPal\Ipn\Response` is no longer abstract
- rename `status` property on `PayPal\Ipn\Response` to `statusCode`. Getter / setters also renamed
- rename `getStatusReport` method on `PayPal\Ipn\Listener` to `getReport`
- add basic listener test
- misc refactoring + cleaning up

## 1.0.0

First release
