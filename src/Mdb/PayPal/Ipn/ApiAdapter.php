<?php

namespace Mdb\PayPal\Ipn;

use GuzzleHttp\ClientInterface;

class ApiAdapter
{
    const API_URI = 'https://www.sandbox.paypal.com/cgi-bin/webscr';

    /**
     * @var ClientInterface
     */
    private $httpClient;

    /**
     * @param ClientInterface $httpClient
     */
    public function __construct(ClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * @param Message $message
     *
     * @return string
     */
    public function verifyIpnMessage(Message $message)
    {
        $requestBody = 'cmd=_notify-validate&' . (string) $message;

        $response = $this->httpClient->post(self::API_URI, array(
            'body' => $requestBody
        ));

        return (string) $response->getBody();
    }
}
