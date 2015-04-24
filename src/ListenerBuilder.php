<?php

namespace Mdb\PayPal\Ipn;

use Symfony\Component\EventDispatcher\EventDispatcher;

abstract class ListenerBuilder
{
    /**
     * @return Listener
     */
    public function build()
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

    /**
     * @return Verifier
     */
    private function getVerifier()
    {
        $service = $this->getService();

        return new Verifier($service);
    }

    /**
     * @return EventDispatcher
     */
    private function getEventDispatcher()
    {
        return new EventDispatcher();
    }

    /**
     * @return MessageFactory
     */
    abstract protected function getMessageFactory();

    /**
     * @return Service
     */
    abstract protected function getService();
}
