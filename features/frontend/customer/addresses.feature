@frontend @customer @address
Feature: Edit profile information
    In order to manage my addresses
    As a customer
    I want to be able to connect to my personal account

    Background:
        Given I am connected with "johndoe" and "johndoe" on "login"

    @200
    Scenario: Connect as customer to access my addresses
        When I go to "/shop/user/address"
        Then I should see "Addresses"
        And I should see "Delivery"
        And I should see "Billing"
        And I should see "Contact"
        And the response status code should be 200

    @200
    Scenario: Add a new delivery address
        When I go to "/shop/user/address"
        And I press "Submit"
        Then I should see "Addresses"
        And I should see "Delivery"
        And I should see "Billing"
        And I should see "Contact"
        And the response status code should be 200

    # TODO
    # A brand new account has no addresses
    # Adding and removing addresses
