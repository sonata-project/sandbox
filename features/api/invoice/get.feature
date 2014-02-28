@api @get @invoice @ecommerce
Feature: Check the GET API calls for InvoiceBundle

  Scenario: Check invoice list (json)
    Given I am authenticating as "admin" with "admin" password
    When  I send a GET request to "/api/ecommerce/invoices.json"
    Then  the response code should be 200
    And   the response should contain json
    Then  response should contain "payment_method"

  Scenario: Check invoice list (xml)
    Given I am authenticating as "admin" with "admin" password
    When  I send a GET request to "/api/ecommerce/invoices.xml"
    Then  the response code should be 200
    And   the response should contain XML
    Then  response should contain "payment_method"

  Scenario: Check unique invoice (json)
    Given I am authenticating as "admin" with "admin" password
    When  I send a GET request to "/api/ecommerce/invoices/1.json"
    Then  the response code should be 200
    And   the response should contain json
    Then  response should contain "payment_method"

  Scenario: Check unique invoice (xml)
    Given I am authenticating as "admin" with "admin" password
    When  I send a GET request to "/api/ecommerce/invoices/1.xml"
    Then  the response code should be 200
    And   the response should contain XML
    Then  response should contain "payment_method"

  Scenario: Check invoice elements (json)
    Given I am authenticating as "admin" with "admin" password
    When  I send a GET request to "/api/ecommerce/invoices/1/invoiceelements.json"
    Then  the response code should be 200
    And   the response should contain json
    Then  response should contain "order_element_id"

  Scenario: Check invoice elements (xml)
    Given I am authenticating as "admin" with "admin" password
    When  I send a GET request to "/api/ecommerce/invoices/1/invoiceelements.xml"
    Then  the response code should be 200
    And   the response should contain XML
    Then  response should contain "order_element_id"
