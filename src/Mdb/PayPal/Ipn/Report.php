<?php

namespace Mdb\PayPal\Ipn;

class Report
{
    /**
     * @var Message
     */
    private $ipnMessage;

    /**
     * @param Message $ipnMessage
     */
    public function setIpnMessage(Message $ipnMessage)
    {
        $this->ipnMessage = $ipnMessage;
    }

    /**
     * @return Message
     */
    public function getIpnMessage()
    {
        return $this->ipnMessage;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $report = <<<REPORT
VERIFICATION REQUEST POST DATA:
-------------------------------

$this->ipnMessage

IPN MESSAGE:
------------

{$this->getIpnMessageKeyValueListString()}

REPORT;

        return $report;
    }

    /**
     * @return string
     */
    private function getIpnMessageKeyValueListString()
    {
        $str = '';

        $data = $this->ipnMessage->getAll();

        foreach ($data as $k => $v) {
            $str .= sprintf("%s = %s\n", $k, $v);
        }

        return rtrim($str, "\n");
    }
}
