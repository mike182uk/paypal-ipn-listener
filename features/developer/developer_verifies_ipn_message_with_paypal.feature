Feature: Developer verifies IPN message with PayPal
  In order to trust the contents an IPN message
  As a developer
  I want to verify an IPN message with PayPal

  @invalidIpn
  Scenario: IPN message fails verification
    Given I have received an IPN message
    When I verify the IPN message with PayPal
    Then PayPal should report that the IPN message is untrustworthy

  @verifiedIpn
  Scenario: IPN message passes verification
    Given I have received an IPN message
    When I verify the IPN message with PayPal
    Then PayPal should report that the IPN message is trustworthy
