<?php

use ListenerBuilder\Guzzle\ArrayListenerBuilder;

class GuzzleArrayListenerBuilderContext extends FeatureContext
{
    /**
     * {@inheritdoc}
     */
    protected function getListener()
    {
        $listenerBuilder = new ArrayListenerBuilder();

        $listenerBuilder->setServiceEndpoint(
            $this->getServiceEndpoint()
        );
        $listenerBuilder->setData($this->ipnMessageData);

        return $listenerBuilder->build();
    }
}
