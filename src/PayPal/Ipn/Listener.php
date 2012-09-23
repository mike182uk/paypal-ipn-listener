<?php

namespace PayPal\Ipn;

class Listener
{
    /**
     * Request object to be used to make the request to PayPal
     *
     * @var object
     */
    private $request;

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
        //cache the request object
        $request =& $this->request;

        //send the request
        $request->send();

        //cache response object
        $response = $request->getResponse();

        //cache response values
        $responseStatus = $response->getStatus();
        $responseBody = $response->getBody();

        //make sure 200 response received
        if ($responseStatus != 200) {
            throw new UnexpectedResponseStatusException('Invalid response status: ' . $responseStatus);
        }

        //check the response body
        if (strpos($responseBody, 'VERIFIED') !== false) {
            return true;
        } elseif (strpos($responseBody, 'INVALID') !== false) {
            return false;
        } else {
            throw new UnexpectedResponseBodyException('Unexpected body response received');
        }
    }

    /**
     * Get a text based report on the latest IPN request
     *
     * @return string
     */
    public function getStatusReport()
    {
        //output
        $output = '';
        $dashLine = function($length = 80) {
            $l = '';
            for ($i = 0; $i < $length; $i++) { $l .= '-'; }

            return $l;
        };
        $linebreak = "\n";
        $newline = function($data) use (&$output, &$linebreak) {
            $output .= $data . $linebreak;
        };

        //data
        $request = $this->request;
        $response = $request->getResponse();

        //generate status report
        $newline($dashLine());
        $newline('[' . date('d/m/Y H:i:s') . '] - ' . $request->getRequestUri());
        $newline($dashLine() . $linebreak);

        $newline('RESPONSE STATUS: ');
        $newline($dashLine(16) . $linebreak);
        $newline($response->getStatus() . $linebreak);

        $newline('RESPONSE BODY: ');
        $newline($dashLine(14) . $linebreak);

        $newline($response->getBody() . $linebreak);

        $newline('POST: ');
        $newline($dashLine(5) . $linebreak);

        $newline($request->getEncodedData() . $linebreak);

        $newline('USER POST VARS: ');
        $newline($dashLine(15) . $linebreak);

        foreach ($request->getData() as $k => $v) {
            $newline($k . ' = ' . $v);
        }

        return $output;
    }
}
