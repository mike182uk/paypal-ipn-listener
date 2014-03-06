<?php

namespace PayPal\Ipn\Verifier;

use PayPal\Ipn\Verifier;
use PayPal\Ipn\VerificationResponse;
use RuntimeException;
use Exception;

class SocketVerifier extends Verifier
{
    /**
     * Send the IPN verification request to PayPal.
     *
     * @return VerificationResponse
     * @throws RuntimeException
     */
    public function sendVerificationRequest()
    {
        $port = $this->useSSL ? '443' : '80';
        $uri = $this->getRequestUri();

        try {
            $fp = fsockopen($uri, $port, $errno, $error, $this->timeout);
        } catch (Exception $e) {
            throw new RuntimeException(sprintf('fsockopen error: [%d] %s.', $errno, $error));
        }

        $data = 'cmd=_notify-validate&' . $this->ipnMessage;

        $headers = "POST /cgi-bin/webscr HTTP/1.1\r\n";
        $headers .= "Host: " . $this->getHost() . "\r\n";
        $headers .= "Content-Type: application/x-www-form-urlencoded\r\n";
        $headers .= "Content-Length: " . strlen($data) . "\r\n";
        $headers .= "Connection: Close\r\n\r\n";

        fputs($fp, sprintf('%s%s%s', $headers, $data, "\r\n\r\n"));

        $responseBody = '';
        $responseStatusCode = 0;

        while (!feof($fp)) {
            if (empty($responseBody)) {
                $responseBody .= $responseStatusCode = fgets($fp, 1024); // extract HTTP status code from first line
                $responseStatusCode = trim(substr($responseStatusCode, 9, 4));
            } else {
                $responseBody .= fgets($fp, 1024);
            }
        }

        fclose($fp);

        return new VerificationResponse($responseBody, $responseStatusCode);
    }

    /**
     * Get the URI to be used to make the request to.
     *
     * @return string
     */
    public function getRequestUri()
    {
        $prefix = $this->useSSL ? 'ssl://' : '';

        return sprintf('%s%s', $prefix, $this->host);
    }
}
