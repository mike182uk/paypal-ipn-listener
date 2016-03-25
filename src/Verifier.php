<?php

namespace Mdb\PayPal\Ipn;

use Http\Client\HttpClient;
use Http\Message\MessageFactory;

class Verifier
{
    const STATUS_KEYWORD_VERIFIED = 'VERIFIED';
    const STATUS_KEYWORD_INVALID = 'INVALID';

    /**
     * @var HttpClient
     */
    private $httpClient;

    /**
     * @var MessageFactory
     */
    private $messageFactory;

    /**
     * @var bool
     */
    private $useSandbox;

    public function __construct(HttpClient $httpClient, MessageFactory $messageFactory, $useSandbox = false)
    {
        $this->httpClient = $httpClient;
        $this->messageFactory = $messageFactory;
        $this->useSandbox = $useSandbox;
    }

    /**
     * @param array $datas
     *
     * @return bool
     *
     * @throws \UnexpectedValueException
     */
    public function verify(array $datas)
    {
        $body = $this->httpClient->sendRequest($this->createRequest($datas))->getBody();

        $pattern = sprintf('/(%s|%s)/', self::STATUS_KEYWORD_VERIFIED, self::STATUS_KEYWORD_INVALID);

        if (!preg_match($pattern, $body)) {
            throw new \UnexpectedValueException(sprintf('Unexpected verification status encountered: %s', $body));
        }

        return $body === self::STATUS_KEYWORD_VERIFIED;
    }

    /**
     * @param array $datas
     *
     * @return \Psr\Http\Message\RequestInterface
     */
    protected function createRequest(array $datas)
    {
        $body = \GuzzleHttp\Psr7\build_query(['cmd' => '_notify-validate'] + $datas, PHP_QUERY_RFC1738);

        return $this->messageFactory->createRequest('POST', $this->getServiceEndpoint(), [], $body);
    }

    /**
     * @return string
     */
    protected function getServiceEndpoint()
    {
        return $this->useSandbox ?
            'https://www.sandbox.paypal.com/cgi-bin/webscr' :
            'https://www.paypal.com/cgi-bin/webscr';
    }
}
