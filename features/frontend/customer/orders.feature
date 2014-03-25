@frontend @customer @order
Feature: Edit orders from my profile
  In order to manage my orders
  As a customer
  I want to be able to connect to my personal account

  Background:
    Given I am connected with "johndoe" and "johndoe" on "login"

  @200 @order
  Scenario: Connect as customer to access my orders
    When I go to "/shop/user/order"
    Then I should see "Current orders"
    And I should see "Past orders"
    And the response status code should be 200