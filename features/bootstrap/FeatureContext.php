<?php

use Assert\Assertion;
use Behat\Behat\Context\SnippetAcceptingContext;
use GuzzleHttp\Client;
use Mdb\PayPal\Ipn\MessageFactory\ArrayMessageFactory;
use Mdb\PayPal\Ipn\Event\MessageVerificationEvent;
use Mdb\PayPal\Ipn\Event\MessageVerificationFailureEvent;
use Mdb\PayPal\Ipn\Listener;
use Mdb\PayPal\Ipn\Service\GuzzleService;
use Mdb\PayPal\Ipn\Verifier;
use Symfony\Component\EventDispatcher\EventDispatcher;

class FeatureContext implements SnippetAcceptingContext
{
    const SERVICE_ENDPOINT = 'http://localhost';
    const SERVICE_ENDPOINT_PORT_ENV_VAR = 'MOCK_SERVER_PORT';

    /**
     * @var array
     */
    protected $ipnMessageData = [];

    /**
     * @var bool
     */
    private $invalidIpn = false;

    /**
     * @var bool
     */
    private $verifiedIpn = false;

    /**
     * @beforeScenario @invalidIpn
     */
    public function willFailVerification()
    {
        $this->ipnMessageData = [
            '__verified' => 0,
        ];
    }

    /**
     * @beforeScenario @verifiedIpn
     */
    public function willPassVerification()
    {
        $this->ipnMessageData = [
            '__verified' => 1,
        ];
    }

    /**
     * @afterScenario
     */
    public function resetIpnStatus()
    {
        $this->invalidIpn = false;
        $this->verifiedIpn = false;
    }

    /**
     * @Given I have received an IPN message
     */
    public function iHaveReceivedAnIpnMessage()
    {
        $data = [
            'foo' => 'bar',
            'baz' => 'qux',
        ];

        $this->ipnMessageData = array_merge($this->ipnMessageData, $data);
    }

    /**
     * @When I verify the IPN message with PayPal
     */
    public function iVerifyTheIpnMessageWithPaypal()
    {
        $listener = $this->getListener();

        $that = $this;

        $listener->onInvalid(function (MessageVerificationEvent $event) use ($that) {
            $that->invalidIpn = true;
        });

        $listener->onVerified(function (MessageVerificationEvent $event) use ($that) {
            $that->verifiedIpn = true;
        });

        $listener->onVerificationFailure(function (MessageVerificationFailureEvent $event) {
            throw new Exception(
                sprintf('Failed to verify IPN: %s', $event->getError())
            );
        });

        $listener->listen();
    }

    /**
     * @Then PayPal should report that the IPN message is untrustworthy
     */
    public function paypalShouldReportThatTheIpnMessageIsUntrustworthy()
    {
        Assertion::true($this->invalidIpn);
    }

    /**
     * @Then PayPal should report that the IPN message is trustworthy
     */
    public function paypalShouldReportThatTheIpnMessageIsTrustworthy()
    {
        Assertion::true($this->verifiedIpn);
    }

    /**
     * @return Listener
     */
    protected function getListener()
    {
        $service = new GuzzleService(
            new Client(),
            $this->getServiceEndpoint()
        );

        $verifier = new Verifier($service);

        return new Listener(
            new ArrayMessageFactory($this->ipnMessageData),
            $verifier,
            new EventDispatcher()
        );
    }

    /**
     * @return string
     */
    protected function getServiceEndpoint()
    {
        return sprintf('%s:%s', self::SERVICE_ENDPOINT, getenv(self::SERVICE_ENDPOINT_PORT_ENV_VAR));
    }
}
