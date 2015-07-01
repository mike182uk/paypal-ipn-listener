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
