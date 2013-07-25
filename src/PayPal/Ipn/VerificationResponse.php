<?php

namespace PayPal\Ipn;

class VerificationResponse
{
    /**
     * Verification response body
     *
     * @var string
     */
    protected $body;

    /**
     * Verification response status code
     *
     * @var integer
     */
    protected $statusCode;

    /**
     * Create a new instance of the verification response
     *
     * @return void
     */
    public function __construct($body, $statusCode)
    {
        $this->body = $body;
        $this->statusCode = (int) $statusCode;
    }

    /**
     * Get the verification response body
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Get the verification response status code
     *
     * @return string
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }
}
