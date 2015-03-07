<?php

namespace spec\Mdb\PayPal\Ipn;

use Mdb\PayPal\Ipn\Message;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ReportSpec extends ObjectBehavior
{
    function it_has_a_mutable_ipn_message(
        Message $message
    )
    {
        $this->setIpnMessage($message);

        $this->getIpnMessage()->shouldReturn($message);
    }

    function it_can_be_represented_as_a_string(
        Message $message
    )
    {
        $message->getAll()->willReturn(array(
            'foo' => 'bar',
            'baz' => 'quz'
        ));

        $message->__toString()->willReturn('foo=bar&bar=baz');

        $this->setIpnMessage($message);

        $reportString = <<<REPORT
VERIFICATION REQUEST POST DATA:
-------------------------------

foo=bar&bar=baz

IPN MESSAGE:
------------

foo = bar
baz = quz

REPORT;

        $this->__toString()->shouldReturn($reportString);
    }
}
