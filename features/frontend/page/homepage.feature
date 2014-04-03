@frontend @homepage
Feature: Check the homepage
  In order to go to the shop
  As an anonymous user
  I need to be able to surf on the website through the homepage

  @200
  Scenario: Check homepage availability
    When I go to "/"
    Then I should see "Welcome"
    And I should see "New products"
    And I should see "Want to stay in touch with us?"
    And I should see "HANDCRAFTED IN PARIS"
