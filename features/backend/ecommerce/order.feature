@backend @ecommerce
Feature: Check orders administration in backend

  @200
  Scenario: Check pages security when user is not logged in
    Given I am on "admin/app/commerce-order/list"
    Then I should see "Authentication"

  Scenario: Check pages security when user is logged in
    Given I am connected with "admin" and "admin" on "admin/app/commerce-order/list"
    Then I should see "Filter"