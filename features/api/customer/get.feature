@api @get @customer @ecommerce
Feature: Check the GET API calls for CustomerBundle

  Scenario: Check customer list (json)
    Given I am authenticating as "admin" with "admin" password
    When  I send a GET request to "/api/ecommerce/customers.json"
    Then  the response code should be 200
    And   the response should contain json
    Then  response should contain "birth_date"

  Scenario: Check customer list (xml)
    Given I am authenticating as "admin" with "admin" password
    When  I send a GET request to "/api/ecommerce/customers.xml"
    Then  the response code should be 200
    And   the response should contain XML
    Then  response should contain "birth_date"

  Scenario: Check unique customer (json)
    Given I am authenticating as "admin" with "admin" password
    When  I send a GET request to "/api/ecommerce/customers/1.json"
    Then  the response code should be 200
    And   the response should contain json
    Then  response should contain "birth_date"

  Scenario: Check unique customer (xml)
    Given I am authenticating as "admin" with "admin" password
    When  I send a GET request to "/api/ecommerce/customers/1.xml"
    Then  the response code should be 200
    And   the response should contain XML
    Then  response should contain "birth_date"

  Scenario: Check customer addresses (json)
    Given I am authenticating as "admin" with "admin" password
    When  I send a GET request to "/api/ecommerce/customers/1/addresses.json"
    Then  the response code should be 200
    And   the response should contain json
    Then  response should contain "My billing address"

  Scenario: Check customer addresses (xml)
    Given I am authenticating as "admin" with "admin" password
    When  I send a GET request to "/api/ecommerce/customers/1/addresses.xml"
    Then  the response code should be 200
    And   the response should contain XML
    Then  response should contain "My billing address"

  Scenario: Check customer orders (json)
    Given I am authenticating as "admin" with "admin" password
    When  I send a GET request to "/api/ecommerce/customers/1/orders.json"
    Then  the response code should be 200
    And   the response should contain json
    Then  response should contain "payment_status"

  Scenario: Check customer orders (xml)
    Given I am authenticating as "admin" with "admin" password
    When  I send a GET request to "/api/ecommerce/customers/1/orders.xml"
    Then  the response code should be 200
    And   the response should contain XML
    Then  response should contain "payment_status"
