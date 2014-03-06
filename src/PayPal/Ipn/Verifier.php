<?php

namespace PayPal\Ipn;

use UnexpectedValueException;
use RuntimeException;

abstract class Verifier
{
    /**
     * PayPal sandbox host.
     */
    const SANDBOX_HOST = 'www.sandbox.paypal.com';

    /**
     * PayPal production host.
     */
    const PRODUCTION_HOST = 'www.paypal.com';

    /**
     * IPN message instance.
     *
     * @var null|Message
     */
    protected $ipnMessage = null;

    /**
     * Host to make IPN verification request to.
     *
     * @var string
     */
    protected $host = self::PRODUCTION_HOST;

    /**
     * Flag to indicate whether IPN verification request should be made over SSL or not.
     *
     * @var boolean
     */
    protected $useSSL = true;

    /**
     * Amount of time (in seconds) to wait for the PayPal server to respond
     * to IPN verification request before timing out.
     *
     * @var integer
     */
    protected $timeout = 30;

    /**
     * IPN verification response instance.
     *
     * @var null|VerificationResponse
     */
    protected $verificationResponse = null;

    /**
     * Set the IPN message.
     *
     * @param Message $ipnMessage
     */
    public function setIpnMessage(Message $ipnMessage)
    {
        $this->ipnMessage = $ipnMessage;
    }

    /**
     * Get the IPN message instance.
     *
     * @return Message
     */
    public function getIpnMessage()
    {
        return $this->ipnMessage;
    }

    /**
     * Get the IPN verification response instance.
     *
     * @return VerificationResponse
     */
    public function getVerificationResponse()
    {
        return $this->verificationResponse;
    }

    /**
     * Set the environment.
     *
     * @param string $environment
     */
    public function setEnvironment($environment)
    {
        $this->host = ($environment == 'production') ? self::PRODUCTION_HOST : self::SANDBOX_HOST;
    }

    /**
     * Get the environment based on the current host set.
     *
     * @return string
     */
    public function getEnvironment()
    {
        return $this->host == self::PRODUCTION_HOST ? 'production' : 'sandbox';
    }

    /**
     * Get the host to be used for the IPN verification request.
     *
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * Set whether the IPN verification request should be sent over SSL or not.
     *
     * @param boolean $useSSL
     */
    public function secure($useSSL)
    {
        $this->useSSL = (bool) $useSSL;
    }

    /**
     * Set the timeout for the IPN verification request.
     *
     * @param integer $timeout
     */
    public function setTimeout($timeout)
    {
        $this->timeout = (int) $timeout;
    }

    /**
     * Get the URI to be used to make the IPN verification request to.
     *
     * @return string
     */
    public function getRequestUri()
    {
        $prefix = $this->useSSL ? 'https://' : 'http://';

        return sprintf('%s%s/cgi-bin/webscr', $prefix, $this->host);
    }

    /**
     * Verify the IPN message.
     *
     * @return boolean
     * @throws UnexpectedValueException
     * @throws RuntimeException
     */
    public function verify()
    {
        // make sure IPN message has been set
        if (is_null($this->ipnMessage)) {
            throw new RuntimeException('IPN message has not been set.');
        }

        $this->verificationResponse = $this->sendVerificationRequest();

        $statusCode = $this->verificationResponse->getStatusCode();
        $body = $this->verificationResponse->getBody();

        if ($statusCode !== 200) {
            throw new UnexpectedValueException(sprintf('Unexpected status code: [%d] received.', $statusCode));
        }

        if (strpos($body, 'VERIFIED') !== false) {
            return true;
        } elseif (strpos($body, 'INVALID') !== false) {
            return false;
        } else {
            throw new UnexpectedValueException('Response body does not contain VERIFIED or INVALID keywords.');
        }
    }

    /**
     * Send the IPN verification request to PayPal.
     *
     *  @return VerificationResponse
     */
    abstract public function sendVerificationRequest();
}
