<?php

namespace PayPal\Ipn\Verifier;

use PayPal\Ipn\Verifier;
use PayPal\Ipn\VerificationResponse;
use RuntimeException;

class CurlVerifier extends Verifier
{
    /**
     * Flag to indicate whether curl follow location headers in the response
     *
     * @var bool
     */
    protected $followLocation = false;

    /**
     * Flag to indicate whether curl should use SSL v3
     *
     * @var bool
     */
    protected $forceSSLv3 = true;

    /**
     * Create an instance of the curl verifier, check curl is enabled
     *
     * @return void
     */
    public function __construct()
    {
        if (!$this->curlEnabled()) {
            throw new RuntimeException('Curl extension is either not enabled or not installed');
        }
    }

    /**
     * Set whether curl will use the CURLOPT_FOLLOWLOCATION to follow any
     * location headers in the response
     *
     * @param  bool $followLocation
     * @return void
     */
    public function followLocation($followLocation)
    {
        $this->followLocation = (bool) $followLocation;
    }

    /**
     * Explicitly set curl to use SSL version 3. Use this if cURL
     * is compiled with GnuTLS SSL.
     *
     * @param  bool $forceSSLv3
     * @return void
     */
    public function forceSSLv3($forceSSLv3)
    {
        $this->forceSSLv3 = (bool) $forceSSLv3;
    }

    /**
     * Send the IPN verification request to PayPal
     *
     * @return \PayPal\Ipn\VerificationResponse
     * @throws \RuntimeException
     */
    public function sendVerificationRequest()
    {
        $ch = curl_init();

        $data = 'cmd=_notify-validate&' . $this->ipnMessage;

        curl_setopt($ch, CURLOPT_URL, $this->getRequestUri());
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, $this->followLocation);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);

        if ($this->forceSSLv3) {
            curl_setopt($ch, CURLOPT_SSLVERSION, 3);
        }

        $responseBody = curl_exec($ch);
        $responseStatusCode = strval(curl_getinfo($ch, CURLINFO_HTTP_CODE));

        if ($responseBody === false or $responseStatusCode == '0') {
            $errno = curl_errno($ch);
            $error = curl_error($ch);
            throw new RuntimeException(sprintf('Curl error: [%d] %s', $errno, $error));
        }

        return new VerificationResponse($responseBody, $responseStatusCode);
    }

    /**
     * Check curl is enabled on the system
     *
     * @return bool
     */
    protected function curlEnabled()
    {
        return function_exists('curl_version');
    }
}
