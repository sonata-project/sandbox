@api @get @basket @ecommerce
Feature: Check the GET API calls for BasketBundle

  Scenario: Check basket list (json)
    Given I am authenticating as "admin" with "admin" password
    When  I send a GET request to "/api/ecommerce/baskets.json"
    Then  the response code should be 200
    And   the response should contain json

  Scenario: Check basket list (xml)
    Given I am authenticating as "admin" with "admin" password
    When  I send a GET request to "/api/ecommerce/baskets.xml"
    Then  the response code should be 200
    And   the response should contain XML

  Scenario: Check unique basket (json)
    Given I am authenticating as "admin" with "admin" password
    When  I send a GET request to "/api/ecommerce/baskets/1.json"
    Then  the response code should be 404

  Scenario: Check unique basket (xml)
    Given I am authenticating as "admin" with "admin" password
    When  I send a GET request to "/api/ecommerce/baskets/1.xml"
    Then  the response code should be 404

  Scenario: Check basket elements (json)
    Given I am authenticating as "admin" with "admin" password
    When  I send a GET request to "/api/ecommerce/baskets/1/basketelements.json"
    Then  the response code should be 404

  Scenario: Check basket elemets (xml)
    Given I am authenticating as "admin" with "admin" password
    When  I send a GET request to "/api/ecommerce/baskets/1/basketelements.xml"
    Then  the response code should be 404
