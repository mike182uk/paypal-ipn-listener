<?php

namespace Mdb\PayPal\Ipn;

interface Service
{
    /**
     * @param Message $message
     *
     * @return ServiceResponse
     *
     * @throws ServiceException
     */
    public function verifyIpnMessage(Message $message);
}
