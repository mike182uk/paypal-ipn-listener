<?php

namespace Mdb\PayPal\Ipn;

use Http\Client\HttpClient;
use Http\Message\MessageFactory;

class Verifier
{
    const PAYPAL_ENDPOINT = 'https://www.paypal.com/cgi-bin/webscr';
    const PAYPAL_SANDBOX_ENDPOINT = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
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
        $body = $this->httpClient->sendRequest($this->createRequest($datas))->getBody()->getContents();

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
        $body = $this->buildQuery(['cmd' => '_notify-validate'] + $datas);

        return $this->messageFactory->createRequest('POST', $this->getServiceEndpoint(), [], $body);
    }

    /**
     * @return string
     */
    protected function getServiceEndpoint()
    {
        return $this->useSandbox ?
            self::PAYPAL_SANDBOX_ENDPOINT :
            self::PAYPAL_ENDPOINT;
    }

    private function buildQuery(array $params)
    {
        if (!$params) {
            return '';
        }

        $qs = '';
        foreach ($params as $k => $v) {
            $k = urlencode($k);
            if (!is_array($v)) {
                $qs .= $k;
                if ($v !== null) {
                    $qs .= '='.urlencode($v);
                }
                $qs .= '&';
            } else {
                foreach ($v as $vv) {
                    $qs .= $k;
                    if ($vv !== null) {
                        $qs .= '='.urlencode($vv);
                    }
                    $qs .= '&';
                }
            }
        }

        return $qs ? (string) substr($qs, 0, -1) : '';
    }
}
