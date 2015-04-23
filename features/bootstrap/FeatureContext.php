<?php

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\SnippetAcceptingContext;

class FeatureContext implements SnippetAcceptingContext
{
    private $ipnMessageData = array();

    /**
     * @beforeScenario @invalidIpn
     */
    public function willFailVerification()
    {
        $this->ipnMessageData = array(
            '__verified' => 0,
        );
    }

    /**
     * @beforeScenario @verifiedIpn
     */
    public function willPassVerification()
    {
        $this->ipnMessageData = array(
            '__verified' => 1,
        );
    }

    /**
     * @Given I have received an IPN message
     */
    public function iHaveReceivedAnIpnMessage()
    {
        $data = array(
            'foo' => 'bar',
            'baz' => 'qux',
        );

        $this->ipnMessageData = array_merge($this->ipnMessageData, $data);
    }

    /**
     * @When I verify the IPN message with PayPal
     */
    public function iVerifyTheIpnMessageWithPaypal()
    {
        throw new PendingException();
    }

    /**
     * @Then PayPal should report that the IPN message is untrustworthy
     */
    public function paypalShouldReportThatTheIpnMessageIsUntrustworthy()
    {
        throw new PendingException();
    }

    /**
     * @Then PayPal should report that the IPN message is trustworthy
     */
    public function paypalShouldReportThatTheIpnMessageIsTrustworthy()
    {
        throw new PendingException();
    }
}
