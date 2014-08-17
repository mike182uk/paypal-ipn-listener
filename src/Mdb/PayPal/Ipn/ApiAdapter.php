<?php

namespace Mdb\PayPal\Ipn;

use GuzzleHttp\ClientInterface;
use Mdb\PayPal\Ipn\Exception\ApiRequestFailureException;
use RuntimeException;

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
     *
     * @throws ApiRequestFailureException
     */
    public function verifyIpnMessage(Message $message)
    {
        $requestBody = 'cmd=_notify-validate&' . (string) $message;

        try {
            $response = $this->httpClient->post(self::API_URI, array(
                'body' => $requestBody
            ));
        } catch (RuntimeException $e) {
            throw new ApiRequestFailureException(
                sprintf('Failed to communicate with API: %s', $e->getMessage())
            );
        }

        return (string) $response->getBody();
    }
}
