@backend @ecommerce
Feature: Check products administration in backend

  @200
  Scenario Outline: Check pages security depending on user credentials
    Given I am on "admin/sonata/product/product/<page>"
    Then I should see "Username"

    Given I am connected with "admin" and "admin" on "admin/sonata/product/product/<page>"
    Then I should see "Add new"

    Examples:
      | page        |
      | create      |
      | list        |