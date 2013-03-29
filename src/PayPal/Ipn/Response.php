<?php

namespace PayPal\Ipn;

class Response
{
    /**
     * The response body
     *
     * @var string
     */
    protected $body;

    /**
     * The response HTTP status code
     *
     * @var int
     */
    protected $statusCode;

    /**
     * Sets the response body
     *
     * @param string $body
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    /**
     * Sets the response HTTP status code
     *
     * @param int $status
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = (int) $statusCode;
    }

    /**
     * Get the response body
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Get the response status code
     *
     * @return int|string
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }
}
