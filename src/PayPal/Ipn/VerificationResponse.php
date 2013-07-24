<?php

namespace PayPal\Ipn;

class VerificationResponse
{
    /**
     * Verification outcome body
     *
     * @var string
     */
    protected $body;

    /**
     * Verification outcome status code
     *
     * @var integer
     */
    protected $statusCode;

    /**
     * Create a new instance of the verification outcome
     *
     * @return void
     */
    public function __construct($body, $statusCode)
    {
        $this->body = $body;
        $this->statusCode = (int) $statusCode;
    }

    /**
     * Get the verification outcome body
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Get the verification outcome status code
     *
     * @return string
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }
}
