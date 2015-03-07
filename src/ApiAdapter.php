<?php

namespace Mdb\PayPal\Ipn;

use Guzzle\Http\ClientInterface;
use Mdb\PayPal\Ipn\Exception\ApiRequestFailureException;
use RuntimeException;

class ApiAdapter
{
    /**
     * @var ClientInterface
     */
    private $httpClient;

    /**
     * @var string
     */
    private $apiBaseUrl;

    /**
     * @param ClientInterface $httpClient
     * @param string          $apiBaseUrl
     */
    public function __construct(ClientInterface $httpClient, $apiBaseUrl)
    {
        $this->httpClient = $httpClient;
        $this->apiBaseUrl = $apiBaseUrl;
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
        $requestBody = 'cmd=_notify-validate&'.(string) $message;

        try {
            $request = $this->httpClient->post($this->apiBaseUrl, array(), $requestBody);

            $response = $request->send();
        } catch (RuntimeException $e) {
            throw new ApiRequestFailureException(
                sprintf('Failed to communicate with API: %s', $e->getMessage())
            );
        }

        return (string) $response->getBody();
    }
}
