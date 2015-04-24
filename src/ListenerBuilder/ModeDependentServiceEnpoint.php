<?php

namespace Mdb\PayPal\Ipn\ListenerBuilder;

trait ModeDependentServiceEnpoint
{
    /**
     * @var int
     */
    private $useSandbox = 0;

    public function useSandbox()
    {
        $this->useSandbox = 1;
    }

    /**
     * @return string
     */
    protected function getServiceEndpoint()
    {
        return ($this->useSandbox) ?
            'https://www.sandbox.paypal.com/cgi-bin/webscr' :
            'https://ipnpb.paypal.com/cgi-bin/webscr';
    }
}
