<?php

namespace PayPal\Ipn\Request;

use PayPal\Ipn\Exception;

class Socket extends \PayPal\Ipn\Request
{
    /**
     * Sends the request to PayPal
     *
     * @throws SocketException
     */
    public function send()
    {
        $port = $this->useSSL ? '443' : '80';
        $uri = $this->getRequestUri();

        $fp = fsockopen($uri, $port, $errno, $error, $this->timeout);

        if (!$fp) {
            throw new Exception\SocketRequestException('fsockopen error: [' . $errno . '] ' . $error);
        }

        $headers = "POST /cgi-bin/webscr HTTP/1.0\r\n";
        $headers .= "Host: " . $this->getHost() . "\r\n";
        $headers .= "Content-Type: application/x-www-form-urlencoded\r\n";
        $headers .= "Content-Length: ".strlen($this->encodedData)."\r\n";
        $headers .= "Connection: Close\r\n\r\n";

        fputs($fp, $headers . $this->encodedData . "\r\n\r\n");

        $responseBody = '';
        $responseStatus = 0;

        while (!feof($fp)) {
            if (empty($responseBody)) {
                $responseBody .= $responseStatus = fgets($fp, 1024); // extract HTTP status from first line
                $responseStatus = trim(substr($responseStatus, 9, 4));
            } else {
                $responseBody .= fgets($fp, 1024);
            }
        }

        fclose($fp);

        $this->response->setBody($responseBody);
        $this->response->setStatus($responseStatus);
    }

    /**
     * Get the URI to be used to make the request
     *
     * @return string
     */
    public function getRequestUri()
    {
        $prefix = $this->useSSL ? 'ssl://' : '';

        return $prefix . $this->host;
    }
}
