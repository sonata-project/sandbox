@api @get @invoice @ecommerce
Feature: Check the Invoice controller calls for InvoiceBundle

  Background:
    Given I am authenticating as "admin" with "admin" password

  # GET

  @api @invoice @list
  Scenario Outline: Retrieve all available invoices
    When I send a GET request to "<resource>"
    Then the response code should be 200
    And response should contain "<format>" object
    And response pager should display page <page_number> with <per_page> elements
    And response pager data should be consistent

  Examples:
    | resource                                             | format | page_number | per_page |
    | /api/ecommerce/invoices.xml                          | xml    | 1           | 10       |
    | /api/ecommerce/invoices.xml?page=1&count=5           | xml    | 1           | 5        |
    | /api/ecommerce/invoices.json                         | json   | 1           | 10       |
    | /api/ecommerce/invoices.json?page=1&count=5          | json   | 1           | 5        |

  @api @invoice @id
  Scenario Outline:  Retrieve an invoice by an unique ID
    When I send a GET request to "<resource>"
    Then the response code should be <status_code>
    And response should contain "<format>" object
    And response should contain "<message>"

    Examples:
      | resource                       | status_code | format | message        |
      | /api/ecommerce/invoices/1.json | 200         | json   | payment_method |
      | /api/ecommerce/invoices/1.xml  | 200         | xml    | payment_method |

  @api @invoice @unknown
  Scenario Outline: Check unavailable unique invoice
    When I send a GET request to "<resource>"
    Then the response code should be <status_code>
    And response should contain "<format>" object
    And response should contain "<message>"

    Examples:
      | resource                                | status_code | format | message                        |
      | /api/ecommerce/invoices/9999999999.json | 404         | json   | Invoice (9999999999) not found |
      | /api/ecommerce/invoices/9999999999.xml  | 404         | xml    | Invoice (9999999999) not found |


  # INVOICE ELEMENTS

  @api @invoice @invoice_element @list
  Scenario Outline: Check invoice elements
    When I send a GET request to "<resource>"
    Then the response code should be <status_code>
    And response should contain "<format>" object
    And response should contain "<message>"

    Examples:
      | resource                                       | status_code | format | message          |
      | /api/ecommerce/invoices/1/invoiceelements.json | 200         | json   | order_element_id |
      | /api/ecommerce/invoices/1/invoiceelements.xml  | 200         | xml    | order_element_id |

  @api @invoice @invoice_element @unknown
  Scenario Outline: Check invoice elements of an unavailable invoice
    When I send a GET request to "<resource>"
    Then the response code should be <status_code>
    And response should contain "<format>" object
    And response should contain "<message>"

    Examples:
      | resource                                                | status_code | format | message                        |
      | /api/ecommerce/invoices/9999999999/invoiceelements.json | 404         | json   | Invoice (9999999999) not found |
      | /api/ecommerce/invoices/9999999999/invoiceelements.xml  | 404         | xml    | Invoice (9999999999) not found |
