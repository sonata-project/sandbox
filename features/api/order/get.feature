@api @get @order @ecommerce
Feature: Check the GET API calls for OrderBundle

  Scenario: Check order list (json)
    Given I am authenticating as "admin" with "admin" password
    When  I send a GET request to "/api/ecommerce/orders.json"
    Then  the response code should be 200
    And   the response should contain json
    Then  response should contain "payment_method"

  Scenario: Check order list (xml)
    Given I am authenticating as "admin" with "admin" password
    When  I send a GET request to "/api/ecommerce/orders.xml"
    Then  the response code should be 200
    And   the response should contain XML
    Then  response should contain "payment_method"

  Scenario: Check unique order (json)
    Given I am authenticating as "admin" with "admin" password
    When  I send a GET request to "/api/ecommerce/orders/1.json"
    Then  the response code should be 200
    And   the response should contain json
    Then  response should contain "payment_method"

  Scenario: Check unique order (xml)
    Given I am authenticating as "admin" with "admin" password
    When  I send a GET request to "/api/ecommerce/orders/1.xml"
    Then  the response code should be 200
    And   the response should contain XML
    Then  response should contain "payment_method"

  Scenario: Check order elements (json)
    Given I am authenticating as "admin" with "admin" password
    When  I send a GET request to "/api/ecommerce/orders/1/orderelements.json"
    Then  the response code should be 200
    And   the response should contain json
    Then  response should contain "raw_product"

  Scenario: Check order elements (xml)
    Given I am authenticating as "admin" with "admin" password
    When  I send a GET request to "/api/ecommerce/orders/1/orderelements.xml"
    Then  the response code should be 200
    And   the response should contain XML
    Then  response should contain "raw_product"
