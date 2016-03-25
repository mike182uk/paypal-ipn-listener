<?php

namespace Mdb\PayPal\Ipn;

use Http\Message\MessageFactory\DiactorosMessageFactory;
use Http\Message\MessageFactory\GuzzleMessageFactory;
use Http\Message\StreamFactory\DiactorosStreamFactory;
use Http\Message\StreamFactory\GuzzleStreamFactory;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ListenerBuilder
{
    protected $options;

    public function __construct(array $options = [])
    {
        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);

        $this->options = $resolver->resolve($options);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'use_sandbox' => false,
                'httplug.client' => function () {
                    if (class_exists('\Http\Discovery\HttpClientDiscovery')) {
                        return \Http\Discovery\HttpClientDiscovery::find();
                    }

                    if (class_exists('\Http\Adapter\Guzzle6\Client')) {
                        return new \Http\Adapter\Guzzle6\Client();
                    }

                    if (class_exists('\Http\Adapter\Guzzle5\Client')) {
                        return new \Http\Adapter\Guzzle5\Client();
                    }

                    if (class_exists('\Http\Client\Socket\Client')) {
                        return new \Http\Client\Socket\Client();
                    }

                    if (class_exists('\Http\Client\Curl\Client')) {
                        return new \Http\Client\Curl\Client();
                    }

                    if (class_exists('Http\Adapter\Buzz\Client')) {
                        return new \Http\Adapter\Buzz\Client();
                    }

                    throw new \LogicException('The httplug.client could not be guessed. Install one of the following packages: php-http/guzzle6-adapter. You can also overwrite the config option with your implementation.');
                },
                'httplug.message_factory' => function () {
                    if (class_exists('\Http\Discovery\MessageFactoryDiscovery')) {
                        return \Http\Discovery\MessageFactoryDiscovery::find();
                    }

                    if (class_exists('\Zend\Diactoros\Request')) {
                        return new DiactorosMessageFactory();
                    }

                    if (class_exists('\GuzzleHttp\Psr7\Request')) {
                        return new GuzzleMessageFactory();
                    }

                    throw new \LogicException('The httplug.message_factory could not be guessed. Install one of the following packages: php-http/guzzle6-adapter. You can also overwrite the config option with your implementation.');
                },
                'httplug.stream_factory' => function () {
                    if (class_exists('\Http\Discovery\StreamFactoryDiscovery')) {
                        return \Http\Discovery\StreamFactoryDiscovery::find();
                    }

                    if (class_exists('\Zend\Diactoros\Stream')) {
                        return new DiactorosStreamFactory();
                    }

                    if (function_exists('\GuzzleHttp\Psr7\stream_for')) {
                        return new GuzzleStreamFactory();
                    }

                    throw new \LogicException('The httplug.stream_factory could not be guessed. Install one of the following packages: php-http/guzzle6-adapter. You can also overwrite the config option with your implementation.');
                },
            ])
            ->setAllowedTypes('use_sandbox', 'bool')
            ->setAllowedTypes('httplug.client', '\Http\Client\HttpClient')
            ->setAllowedTypes('httplug.message_factory', '\Http\Message\MessageFactory')
            ->setAllowedTypes('httplug.stream_factory', '\Http\Message\StreamFactory');
    }

    /**
     * @return Listener
     */
    public function build()
    {
        return new Listener(
            $this->options['httplug.stream_factory'],
            $this->getVerifier(),
            $this->getEventDispatcher()
        );
    }

    /**
     * @return Verifier
     */
    private function getVerifier()
    {
        return new Verifier(
            $this->options['httplug.client'],
            $this->options['httplug.message_factory'],
            $this->options['use_sandbox']
        );
    }

    /**
     * @return EventDispatcher
     */
    private function getEventDispatcher()
    {
        return new EventDispatcher();
    }
}
