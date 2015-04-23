<?php

use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Mdb\PayPal\Ipn\Message;
use Mdb\PayPal\Ipn\ApiAdapter;
use Mdb\PayPal\Ipn\Verifier;
use Guzzle\Http\Client;

class VerifierContext implements SnippetAcceptingContext
{
    const API_BASE_URL = 'https://www.sandbox.paypal.com/cgi-bin/webscr';

    /**
     * @var Message
     */
    private $message;

    /**
     * @var string
     */
    private $verificationResult;

    /**
     * @Given I have received an IPN message containing:
     */
    public function iHaveReceivedAnIpnMessageContaining(PyStringNode $rawPostData)
    {
        $this->message = new Message($rawPostData);
    }

    /**
     * @When I verify the IPN message with PayPal
     */
    public function iVerifyTheIpnMessageWithPaypal()
    {
        $httpClient = new Client();
        $apiAdapter = new ApiAdapter($httpClient, self::API_BASE_URL);
        $verifier = new Verifier($apiAdapter);

        $this->verificationResult = $verifier->verify($this->message);
    }

    /**
     * @Then PayPal should report that the IPN message is untrustworthy
     */
    public function paypalShouldReportThatTheIpnMessageIsUntrustworthy()
    {
        expect($this->verificationResult)->toBe(false);
    }
}
