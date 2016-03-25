<?php

namespace Mdb\PayPal\Ipn\Event;

use Symfony\Component\EventDispatcher\Event;

abstract class IpnVerificationEvent extends Event
{
    /**
     * @var array
     */
    private $datas;

    /**
     * @param array $datas
     */
    public function __construct(array $datas)
    {
        $this->datas = $datas;
    }

    /**
     * @return array
     */
    public function getDatas()
    {
        return $this->datas;
    }
}
