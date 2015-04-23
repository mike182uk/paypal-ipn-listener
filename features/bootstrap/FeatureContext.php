<?php

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\SnippetAcceptingContext;

class VerifierContext implements SnippetAcceptingContext
class FeatureContext implements SnippetAcceptingContext
{
    /**
     * @Given I have received an IPN message
     */
    public function iHaveReceivedAnIpnMessage()
    {
        throw new PendingException();
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
