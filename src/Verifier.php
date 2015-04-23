<?php

namespace Mdb\PayPal\Ipn;

use UnexpectedValueException;

class Verifier
{
    const STATUS_KEYWORD_VERIFIED = 'VERIFIED';
    const STATUS_KEYWORD_INVALID = 'INVALID';

    /**
     * @var ApiAdapter
     */
    private $apiAdapter;

    /**
     * @param ApiAdapter $apiAdapter
     */
    public function __construct(ApiAdapter $apiAdapter)
    {
        $this->apiAdapter = $apiAdapter;
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
        $apiResponse = $this->apiAdapter->verifyIpnMessage($message);

        if ($apiResponse != self::STATUS_KEYWORD_VERIFIED && $apiResponse != self::STATUS_KEYWORD_INVALID) {
            throw new UnexpectedValueException(sprintf('Unexpected verification status encountered: %s', $apiResponse));
        }

        return $apiResponse === self::STATUS_KEYWORD_VERIFIED;
    }
}
