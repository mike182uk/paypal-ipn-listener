<?php

namespace Mdb\PayPal\Ipn;

class Verifier
{
    const STATUS_KEYWORD_VERIFIED = 'VERIFIED';
    const STATUS_KEYWORD_INVALID = 'INVALID';

    /**
     * @var Service
     */
    private $service;

    /**
     * @param Service $service
     */
    public function __construct(Service $service)
    {
        $this->service = $service;
    }

    /**
     * @param Message $message
     *
     * @return bool
     *
     * @throws UnexpectedValueException
     */
    public function verify(Message $message)
    {
        $serviceResponse = $this->service->verifyIpnMessage($message);
        $serviceResponseBody = $serviceResponse->getBody();

        $pattern = sprintf('/(%s|%s)/', self::STATUS_KEYWORD_VERIFIED, self::STATUS_KEYWORD_INVALID);

        if (!preg_match($pattern, $serviceResponseBody)) {
            throw new \UnexpectedValueException(sprintf('Unexpected verification status encountered: %s', $serviceResponseBody));
        }

        return $serviceResponseBody === self::STATUS_KEYWORD_VERIFIED;
    }
}
