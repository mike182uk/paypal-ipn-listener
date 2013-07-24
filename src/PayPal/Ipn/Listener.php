<?php

namespace PayPal\Ipn;

use PayPal\Ipn\Verifier;
use PayPal\Ipn\Message;
use RuntimeException;
use Closure;

class Listener
{
    /**
     * Verifier instance
     *
     * @var null|\PayPal\Ipn\Verifier
     */
    protected $verifier = null;

    /**
     * Verified IPN callback stack
     *
     * @var array
     */
    protected $verifiedIpnCallbackStack = array();

    /**
     * Invalid IPN callback stack
     *
     * @var array
     */
    protected $invalidIpnCallbackStack = array();

    /**
     * Get the verifier instance
     *
     * @return \PayPal\Ipn\Verifier
     */
    public function getVerifier()
    {
        return $this->verifier;
    }

    /**
     * Set the verifier instance
     *
     * @param  \PayPal\Ipn\Verifier $verifier
     * @return void
     */
    public function setVerifier(Verifier $verifier)
    {
        $this->verifier = $verifier;
    }

    /**
     * Set a callback to be exectuted when the IPN is verified
     *
     * @param  Closure $callback
     * @return void
     */
    public function onVerifiedIpn(Closure $callback)
    {
        $this->verifiedIpnCallbackStack[] = $callback;
    }

    /**
     * Set a callback to be exectuted when the IPN is invalid
     *
     * @param  Closure $callback
     * @return void
     */
    public function onInvalidIpn(Closure $callback)
    {
        $this->invalidIpnCallbackStack[] = $callback;
    }

    /**
     * Verify the IPN
     *
     * @param  bool              $executeCallbacks
     * @return bool
     * @throws \RuntimeException
     */
    public function processIpn($executeCallbacks = true)
    {
        // make sure a verifier has been set
        if (is_null($this->verifier)) {
            throw new RuntimeException('IPN verifier has not been set');
        }

        $ipnVerificationStatus = $this->verifier->verify();

        if ($executeCallbacks) {
            $stack = $ipnVerificationStatus ? $this->verifiedIpnCallbackStack : $this->invalidIpnCallbackStack;

            $this->processCallbackStack($stack);
        }

        return $ipnVerificationStatus;
    }

    /**
     * Listen for an IPN and execute callable based on verification status
     *
     * @param  Closure      $verifiedIpnCallback
     * @param  Closure|null $invalidIpnCallback
     * @return void
     */
    public function listen(Closure $verifiedIpnCallback, Closure $invalidIpnCallback = null)
    {
        $this->onVerifiedIpn($verifiedIpnCallback);

        if (!is_null($invalidIpnCallback)) {
            $this->onInvalidIpn($invalidIpnCallback);
        }

        $this->processIpn();
    }

    /**
     * Execute all callables in a given stack
     *
     * @param  array $stack
     * @return void
     */
    protected function processCallbackStack($stack)
    {
        foreach ($stack as $callback) {
            $callback();
        }
    }

    /**
     * Get a text based report of the latest IPN verification request
     *
     * @return string
     * @throws \RuntimeException
     */
    public function getReport()
    {
        // output
        $output = '';

        // helpers
        $dashLine = function($length = 100) {
            $l = '';
            for ($i = 0; $i < $length; $i++) { $l .= '-'; }

            return $l;
        };
        $linebreak = "\n";
        $newline = function($data) use (&$output, &$linebreak) {
            $output .= $data . $linebreak;
        };

        // make sure a verifier has been set
        if (is_null($this->verifier)) {
            throw new RuntimeException('IPN verifier has not been set');
        }

        // cache objects locally for convience
        $verifier = $this->verifier;
        $ipnMessage = $verifier->getIpnMessage();
        $verificationResponse = $verifier->getVerificationResponse();

        // generate report
        $newline($dashLine());
        $title = sprintf('[%s] - %s (%s)', date('d/m/Y H:i:s'), $verifier->getRequestUri(), ucfirst($verifier->getEnvironment()));
        $newline($title);
        $newline($dashLine() . $linebreak);

        // if a verification request was made
        if (!is_null($verificationResponse)) {
            $newline('VERIFICATION RESPONSE STATUS: ');
            $newline($dashLine(28) . $linebreak);
            $newline($verificationResponse->getStatusCode() . $linebreak);

            $newline('VERIFICATION RESPONSE BODY: ');
            $newline($dashLine(26) . $linebreak);
            $newline($verificationResponse->getBody() . $linebreak);

            $newline('VERIFICATION REQUEST POST DATA: ');
            $newline($dashLine(31) . $linebreak);

            $newline($ipnMessage . $linebreak);

            $newline('IPN MESSAGE: ');
            $newline($dashLine(12) . $linebreak);

            foreach ($ipnMessage as $k => $v) {
                $newline($k . ' = ' . $v);
            }
        } else {
            $newline('VERIFICATION REQUEST NOT MADE');
        }

        return $output;
    }
}
