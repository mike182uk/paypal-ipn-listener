<?php

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Mdb\PayPal\Ipn\Message;
use Mdb\PayPal\Ipn\ApiAdapter;
use Mdb\PayPal\Ipn\Verifier;
use Guzzle\Http\Client;

class VerifierContext implements SnippetAcceptingContext
{
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
    public function iHaveReceivedAnIpnMessageContaining(PyStringNode $string)
    {
        $data = array();
        $keyValuePairs = explode('&', $string);

        foreach ($keyValuePairs as $keyValuePair) {
            list($k, $v) = explode('=', $keyValuePair);

            $data[$k] = $v;
        }

        $this->message = new Message($data);
    }

    /**
     * @When I verify the IPN message with PayPal
     */
    public function iVerifyTheIpnMessageWithPaypal()
    {
        $httpClient = new Client();
        $apiAdapter = new ApiAdapter($httpClient);
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
