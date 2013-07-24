#PayPal IPN Listener [![Build Status](https://secure.travis-ci.org/mike182uk/paypal-ipn-listener.png)](http://travis-ci.org/mike182uk/paypal-ipn-listener)

A PayPal IPN (Instant Payment Notification) listener for PHP >=5.3.0. If you are looking for a < 5.3.0 compatible PayPal IPN Listener i highly recommend - [https://github.com/Quixotix/PHP-PayPal-IPN](https://github.com/Quixotix/PHP-PayPal-IPN) (this package is heavily based around this).

###Features

- Flexible, extensible, component based architecture
- Easily switch between sandbox and production mode
- Generate status reports (request & response)

###Prerequisites

1. PHP >=5.3.0
2. A good understanding of how the PayPal Instant Payment Notification system works. see [https://cms.paypal.com/cms_content/US/en_US/files/developer/IPNGuide.pdf](https://cms.paypal.com/cms_content/US/en_US/files/developer/IPNGuide.pdf)
3. This package can be installed using composer or integrated manually. If you are not using an autoloader make sure you include all of the php files in the ``src`` directory.

***Note:*** *All of the code examples in the rest of this document assume you have the required files above either autoloaded or included manually.*

###Architecture

Todo.

###Workflow

Todo.

###Notes

#####Testing

PayPal provide an Instant Payment Notification (IPN) simulator here: [https://developer.paypal.com/webapps/developer/applications/ipn_simulator](https://developer.paypal.com/webapps/developer/applications/ipn_simulator)

The simulator only tells you if the IPN was sent successfully. To get more information about the status of the IPN (what data was sent, what response it got etc.) you can use the ``getReport()`` to generate a status report. You can then save this to a file or email it to yourself.

