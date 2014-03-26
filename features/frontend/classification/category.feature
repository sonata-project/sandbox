@frontend @category
Feature: Check the categories browsing and security

  Background:
    Given I am on "/"

  @200 @catalog
  Scenario: Check the correct display of catalog
    When I go to "/shop/catalog"
    Then I should see "Catalog"
    And I should see "Goodies"
    And I should see "Travels"
    And I should not see "No products available"
    And the response status code should be 200

  @catalog @category @goodies
  Scenario: Browse catalog through the Goodies' category
    When I go to "/shop/catalog"
    And I follow "Goodies"
    Then I should see "Blue PHP plush"
    And I should see "Green PHP plush"
    And I should see "Orange PHP plush"
    And I should see "PHP tee-shirt"
    And I should see "PHP mug"
    And I should see "Maximum Air Sonata Ultimate Edition"

  @catalog @category @travels
  Scenario: Browse catalog through the Travels' category
    When I go to "/shop/catalog"
    And I follow "Travels"
    Then I should see "Japan tour"
    And I should see "Quebec tour"
    And I should see "Paris tour"
    And I should see "Switzerland tour"

  @catalog @category @dummies
  Scenario: Browse catalog through the Dummies' category
    When I go to "/shop/catalog"
    And I follow "Dummy"
    Then I should see "Dummy 1"
    And I should see "Dummy 2"
    And I should see "Dummy 3"
    And I should see "Dummy 4"
    And I should see "Dummy 5"
    And I should see "Dummy 6"
    And I should see "Dummy 7"
    And I should see "Dummy 8"
    And I should see "Dummy 9"

#  @catalog @category @empty @skipped
#  Scenario: Check browsing non display of empty category
#    When I go to "/shop/catalog"
#    And I follow "Thailand"
#    Then I should see "No products available."

#  @catalog @category @direct
#  Scenario: Check direct access to disabled category
#    When I go to "shop/category/8/shoes"
#    Then I should see "Page not found."
#    And the response status code should be 404