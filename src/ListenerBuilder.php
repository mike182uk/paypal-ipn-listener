<?php

namespace Mdb\PayPal\Ipn;

use Symfony\Component\EventDispatcher\EventDispatcher;

abstract class ListenerBuilder
{
    public function build() : Listener
    {
        $verifier = $this->getVerifier();
        $messageFactory = $this->getMessageFactory();
        $eventDispatcher = $this->getEventDispatcher();

        return new Listener(
            $messageFactory,
            $verifier,
            $eventDispatcher
        );
    }

    private function getVerifier() : Verifier
    {
        $service = $this->getService();

        return new Verifier($service);
    }

    private function getEventDispatcher() : EventDispatcher
    {
        return new EventDispatcher();
    }

    abstract protected function getMessageFactory() : MessageFactory;

    abstract protected function getService() : Service;
}
