<?php

namespace PayPal\Ipn;

use PayPal\Ipn\Response as IpnResponse;

abstract class Request
{
    /**
     * PayPal sandbox host
     */
    const SANDBOX_HOST = 'www.sandbox.paypal.com';

    /**
     * PayPal live host
     */
    const PRODUCTION_HOST = 'www.paypal.com';

    /**
     * Host to make request to
     *
     * @var string
     */
    protected $host;

    /**
     * Data to be used in requested
     *
     * @var array
     */
    protected $data;

    /**
     * URL encoded version of $data to be sent along with request
     *
     * @var string
     */
    protected $encodedData;

    /**
     * Should the request be made over SSL
     *
     * @var bool
     */
    protected $useSSL = true;

    /**
     * Amount of time (in seconds) to wait for the PayPal server to respond
     * before timing out. Default 30 seconds.
     *
     * @var int
     */
    protected $timeout = 30;

    /**
     * Response object used by request object
     *
     * @var object
     */
    protected $response;

    /**
     * Create a new instance
     *
     * @param bool|array  $data        Data to be used in the request. Can pass array of data otherwise $_POST data will be used
     * @param bool|object $responseObj Optional response object to be used by request
     */
    public function __construct($data = false, $responseObj = false)
    {
        $this->response = $responseObj ? $responseObj : new IpnResponse();
        $this->setData($data);
        $this->setHost();
    }

    /**
     * Set data to be used in the request
     *
     * @param bool|array $data Can pass array of data otherwise $_POST data will be used
     */
    public function setData($data = false)
    {
        $this->data = is_array($data) ? $data : $_POST;
        $this->encodedData = $this->encodeData($this->data);
    }

    /**
     * Get data to be used in the request
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set the host that the request will be sent too
     *
     * @param string $environment Can either be 'production' or 'sandbox'. Defaults to 'production'
     */
    public function setHost($environment = 'production')
    {
        $this->host = ($environment == 'production') ? self::PRODUCTION_HOST : self::SANDBOX_HOST;
    }

    /**
     * Get the host to be used for the request
     *
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * Get the encoded data to be sent along with the request
     *
     * @return string
     */
    public function getEncodedData()
    {
        return $this->encodedData;
    }

    /**
     * Get the response object associated with the request
     *
     * @return object
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Set whether the request should be sent over SSL or not
     *
     * @param bool $useSSL
     */
    public function secure($useSSL)
    {
        $this->useSSL = (bool) $useSSL;
    }

    /**
     * Set the timeout for the request
     *
     * @param int $timeout
     */
    public function setTimeout($timeout)
    {
        $this->timeout = (int) $timeout;
    }

    /**
     * Get the URI to be used to make the request
     *
     * @return string
     */
    public function getRequestUri()
    {
        $prefix = $this->useSSL ? 'https://' : 'http://';

        return $prefix . $this->host . '/cgi-bin/webscr';
    }

    /**
     * URL encodes array of data
     *
     * @param array $data
     *
     * @return string
     */
    protected function encodeData($data)
    {
        $encodedData = 'cmd=_notify-validate';
        foreach ($data as $k => $v) {
            $encodedData .= '&' . $k . '=' . urlencode($v);
        }

        return $encodedData;
    }

    /**
     * Request send method to be implemented by classes that extend the Reåquest class
     *
     * @abstract
     * @return mixed
     */
    abstract public function send();
}
