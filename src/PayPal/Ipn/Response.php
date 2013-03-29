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
    protected $status;

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
     * Sets the response HTTP status
     *
     * @param int $status
     */
    public function setStatus($status)
    {
        $this->status = (int) $status;
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
     * Get the response status
     *
     * @return int|string
     */
    public function getStatus()
    {
        return $this->status;
    }
}
