<?php

namespace Mdb\PayPal\Ipn\ListenerBuilder;

trait ModeDependentServiceEndpoint
{
    /**
     * @var bool
     */
    private $useSandbox = false;

    public function useSandbox()
    {
        $this->useSandbox = true;
    }

    protected function getServiceEndpoint() : string
    {
        return ($this->useSandbox) ?
            'https://ipnpb.sandbox.paypal.com/cgi-bin/webscr' :
            'https://ipnpb.paypal.com/cgi-bin/webscr';
    }
}
