#Changelog

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

First realease
