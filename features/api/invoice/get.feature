@api @get @invoice @ecommerce
Feature: Check the API for InvoiceBundle
  I want to test the GET API calls

  Background:
    Given I am authenticating as "admin" with "admin" password

  Scenario Outline: Check invoice list
    When I send a GET request to "<resource>"
    Then the response code should be <status_code>
    And response should contain "<format>" object
    And response should contain "<message>"

    Examples:
      | resource| status_code | format | message |
      | /api/ecommerce/invoices.json | 200 | json | payment_method |
      | /api/ecommerce/invoices.xml  | 200 | xml  | payment_method |

  Scenario Outline: Check unique invoice
    When I send a GET request to "<resource>"
    Then the response code should be <status_code>
    And response should contain "<format>" object
    And response should contain "<message>"

    Examples:
      | resource| status_code | format | message |
      | /api/ecommerce/invoices/1.json | 200 | json | payment_method |
      | /api/ecommerce/invoices/1.xml  | 200 | xml  | payment_method |

  Scenario Outline: Check unavailable unique invoice
    When I send a GET request to "<resource>"
    Then the response code should be <status_code>
    And response should contain "<format>" object
    And response should contain "<message>"

    Examples:
      | resource| status_code | format | message |
      | /api/ecommerce/invoices/120.json | 404 | json | Invoice (120) not found |
      | /api/ecommerce/invoices/120.xml  | 404 | xml | Invoice (120) not found |

  Scenario Outline: Check invoice elements
    When I send a GET request to "<resource>"
    Then the response code should be <status_code>
    And response should contain "<format>" object
    And response should contain "<message>"

    Examples:
      | resource| status_code | format | message |
      | /api/ecommerce/invoices/1/invoiceelements.json | 200 | json | order_element_id |
      | /api/ecommerce/invoices/1/invoiceelements.xml  | 200 | xml  | order_element_id |

  Scenario Outline: Check unavailable invoice elements
    When I send a GET request to "<resource>"
    Then the response code should be <status_code>
    And response should contain "<format>" object
    And response should contain "<message>"

    Examples:
      | resource| status_code | format | message |
      | /api/ecommerce/invoices/120/invoiceelements.json | 404 | json | Invoice (120) not found |
      | /api/ecommerce/invoices/120/invoiceelements.xml  | 404 | xml  | Invoice (120) not found |