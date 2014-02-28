@api @get @page
Feature: Check the GET API calls for PageBundle

  Scenario: Check product list (json)
    Given I am authenticating as "admin" with "admin" password
    When  I send a GET request to "/api/ecommerce/products.json"
    Then  the response code should be 200
    And   the response should contain json
    Then  response should contain "price_including_vat"

  Scenario: Check product list (xml)
    Given I am authenticating as "admin" with "admin" password
    When  I send a GET request to "/api/ecommerce/products.xml"
    Then  the response code should be 200
    And   the response should contain XML
    Then  response should contain "price_including_vat"

  Scenario: Check unique product (json)
    Given I am authenticating as "admin" with "admin" password
    When  I send a GET request to "/api/ecommerce/products/1.json"
    Then  the response code should be 200
    And   the response should contain json
    Then  response should contain "price_including_vat"

  Scenario: Check unique product (xml)
    Given I am authenticating as "admin" with "admin" password
    When  I send a GET request to "/api/ecommerce/products/1.xml"
    Then  the response code should be 200
    And   the response should contain XML
    Then  response should contain "price_including_vat"

  Scenario: Check product categories (json)
    Given I am authenticating as "admin" with "admin" password
    When  I send a GET request to "/api/ecommerce/products/1/categories.json"
    Then  the response code should be 200
    And   the response should contain json
    Then  response should contain "description"

  Scenario: Check product categories (xml)
    Given I am authenticating as "admin" with "admin" password
    When  I send a GET request to "/api/ecommerce/products/1/categories.xml"
    Then  the response code should be 200
    And   the response should contain XML
    Then  response should contain "description"

  Scenario: Check product collections (json)
    Given I am authenticating as "admin" with "admin" password
    When  I send a GET request to "/api/ecommerce/products/1/collections.json"
    Then  the response code should be 200
    And   the response should contain json
    Then  response should contain "description"

  Scenario: Check product collections (xml)
    Given I am authenticating as "admin" with "admin" password
    When  I send a GET request to "/api/ecommerce/products/1/collections.xml"
    Then  the response code should be 200
    And   the response should contain XML
    Then  response should contain "description"

  Scenario: Check product deliveries (json)
    Given I am authenticating as "admin" with "admin" password
    When  I send a GET request to "/api/ecommerce/products/1/deliveries.json"
    Then  the response code should be 200
    And   the response should contain json
    Then  response should contain "product_id"

  Scenario: Check product deliveries (xml)
    Given I am authenticating as "admin" with "admin" password
    When  I send a GET request to "/api/ecommerce/products/1/deliveries.xml"
    Then  the response code should be 200
    And   the response should contain XML
    Then  response should contain "product_id"

  Scenario: Check product packages (json)
    Given I am authenticating as "admin" with "admin" password
    When  I send a GET request to "/api/ecommerce/products/1/packages.json"
    Then  the response code should be 200
    And   the response should contain json
    Then  response should contain "product_id"

  Scenario: Check product packages (xml)
    Given I am authenticating as "admin" with "admin" password
    When  I send a GET request to "/api/ecommerce/products/1/packages.xml"
    Then  the response code should be 200
    And   the response should contain XML
    Then  response should contain "product_id"

  Scenario: Check product productcategories (json)
    Given I am authenticating as "admin" with "admin" password
    When  I send a GET request to "/api/ecommerce/products/1/productcategories.json"
    Then  the response code should be 200
    And   the response should contain json
    Then  response should contain "category_id"

  Scenario: Check product productcategories (xml)
    Given I am authenticating as "admin" with "admin" password
    When  I send a GET request to "/api/ecommerce/products/1/productcategories.xml"
    Then  the response code should be 200
    And   the response should contain XML
    Then  response should contain "category_id"

  Scenario: Check product productcollections (json)
    Given I am authenticating as "admin" with "admin" password
    When  I send a GET request to "/api/ecommerce/products/1/productcollections.json"
    Then  the response code should be 200
    And   the response should contain json
    Then  response should contain "collection_id"

  Scenario: Check product productcollections (xml)
    Given I am authenticating as "admin" with "admin" password
    When  I send a GET request to "/api/ecommerce/products/1/productcollections.xml"
    Then  the response code should be 200
    And   the response should contain XML
    Then  response should contain "collection_id"

  Scenario: Check product variations (json)
    Given I am authenticating as "admin" with "admin" password
    When  I send a GET request to "/api/ecommerce/products/524/variations.json"
    Then  the response code should be 200
    And   the response should contain json
    Then  response should contain "travellers"

  Scenario: Check product variations (xml)
    Given I am authenticating as "admin" with "admin" password
    When  I send a GET request to "/api/ecommerce/products/524/variations.xml"
    Then  the response code should be 200
    And   the response should contain XML
    Then  response should contain "travellers"
