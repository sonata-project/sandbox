@frontend @customer @address
Feature: Edit addresses from my profile
  In order to manage my addresses
  As a customer
  I want to be able to connect to my personal account

  Background:
    Given I am connected with "johndoe" and "johndoe" on "login"

  @200 @address @customer
  Scenario: Connect as customer to access my addresses
    When I go to "/shop/user/address"
    Then I should see "Addresses"
    And I should see "Delivery"
    And I should see "Billing"
    And I should see "Contact"
    And I should not see "You don't have any available addresses."
    And the response status code should be 200

  @address @customer @add @ok
  Scenario Outline: Add an address by type
    When I go to "/shop/user/address/add"
    And I fill in "sonata_customer_address_name" with "<name>"
    And I select "<type>" from "sonata_customer_address_type"
    And I fill in "sonata_customer_address_firstname" with "<firstname>"
    And I fill in "sonata_customer_address_lastname" with "<lastname>"
    And I fill in "sonata_customer_address_address1" with "<address1>"
    And I fill in "sonata_customer_address_postcode" with "<postcode>"
    And I fill in "sonata_customer_address_city" with "<city>"
    And I fill in "sonata_customer_address_countryCode" with "<countrycode>"
    And I fill in "sonata_customer_address_phone" with "<phone>"
    Then I press "Save your address"
    And I should be on "/shop/user/address"
    And I should see "Add an address"
    And I should see "<name>"
    And I should see "<message>"

    Examples:
      | name | type | firstname | lastname | address1 | postcode | city | countrycode | phone | message |
      | Behat Delivery Address | Delivery | Jane | DOE | 6115 Dibbert PortsSibylshire | G4S-3GS | NEW YORK | US | +1 718-123456 | Your new address has been successfully added!
      | Behat Contact Address | Contact | Gwen | INCONNUE | 128, Rue de la Muette | 75017 |PARIS | FR | +33 (0) 123456789 | Your new address has been successfully added!
      | Behat Billing Address | Billing | Joe | UNBEKANNT | 256 Breisacherstraße | 81929 | MUNICH | DE | + 49 0800 89 1234 | Your new address has been successfully added!
