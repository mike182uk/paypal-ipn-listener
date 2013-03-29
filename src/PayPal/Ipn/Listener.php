<?php

namespace PayPal\Ipn;

use PayPal\Ipn\Exception\UnexpectedResponseBodyException;
use PayPal\Ipn\Exception\UnexpectedResponseStatusException;

class Listener
{
    /**
     * Request object to be used to make the request to PayPal
     *
     * @var object
     */
    protected $request;

    /**
     * Create a new instance
     *
     * @param object $request Request object to be used to make the request to PayPal
     * @param string $mode    Can either be 'production' or 'sandbox'. Defaults to 'production'
     */
    public function __construct($request, $mode = 'production')
    {
        $this->request = $request;
        $this->setMode($mode);
    }

    /**
     * Set mode to use for communicating with PayPal. Can either be 'production' or 'sandbox'. Defaults to 'production'
     *
     * @param string $mode
     */
    public function setMode($mode)
    {
        $this->request->setHost($mode);
    }

    /**
     * Verify the IPN message with PayPal
     *
     * @return bool
     * @throws UnexpectedResponseBodyException
     * @throws UnexpectedResponseStatusException
     */
    public function verifyIpn()
    {
        // cache the request object
        $request =& $this->request;

        // send the request
        $request->send();

        // cache response object
        $response = $request->getResponse();

        // cache response values
        $responseStatus = $response->getStatusCode();
        $responseBody = $response->getBody();

        // make sure 200 response received
        if ($responseStatus != 200) {
            throw new UnexpectedResponseStatusException(sprintf('Unexpected response status: %d received',  $responseStatus));
        }

        // check the response body
        if (strpos($responseBody, 'VERIFIED') !== false) {
            return true;
        } elseif (strpos($responseBody, 'INVALID') !== false) {
            return false;
        } else {
            throw new UnexpectedResponseBodyException('Unexpected response body received');
        }
    }

    /**
     * Get a text based report on the latest IPN request
     *
     * @return string
     */
    public function getReport()
    {
        // output
        $output = '';

        // helpers
        $dashLine = function($length = 80) {
            $l = '';
            for ($i = 0; $i < $length; $i++) { $l .= '-'; }

            return $l;
        };
        $linebreak = "\n";
        $newline = function($data) use (&$output, &$linebreak) {
            $output .= $data . $linebreak;
        };

        // cache request + response objects
        $request = $this->request;
        $response = $request->getResponse();

        // generate report
        $newline($dashLine());
        $newline('[' . date('d/m/Y H:i:s') . '] - ' . $request->getRequestUri());
        $newline($dashLine() . $linebreak);

        $newline('RESPONSE STATUS: ');
        $newline($dashLine(16) . $linebreak);
        $newline($response->getStatusCode() . $linebreak);

        $newline('RESPONSE BODY: ');
        $newline($dashLine(14) . $linebreak);

        $newline($response->getBody() . $linebreak);

        $newline('RAW POST: ');
        $newline($dashLine(9) . $linebreak);

        $newline($request->getEncodedData() . $linebreak);

        $newline('POST VARIABLES: ');
        $newline($dashLine(15) . $linebreak);

        foreach ($request->getData() as $k => $v) {
            $newline($k . ' = ' . $v);
        }

        return $output;
    }
}
