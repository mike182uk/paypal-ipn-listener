<?php

namespace Mdb\PayPal\Ipn;

interface Service
{
    /**
     * @throws ServiceException
     */
    public function verifyIpnMessage(Message $message) : ServiceResponse;
}
