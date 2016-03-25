<?php

namespace Mdb\PayPal\Ipn\Event;

class IpnVerificationFailureEvent extends IpnVerificationEvent
{
    /**
     * @var string
     */
    private $error;

    /**
     * @param array $datas
     * @param $error
     */
    public function __construct($datas, $error)
    {
        $this->error = $error;

        parent::__construct($datas);
    }

    /**
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }
}
