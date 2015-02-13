@api @get @order @ecommerce
Feature: Check the Order controller calls for OrderBundle

  Background:
    Given I am authenticating as "admin" with "admin" password

  # GET

  @api @order @list
  Scenario Outline: Retrieve all available orders
    When I send a GET request to "<resource>"
    Then the response code should be 200
    And response should contain "<format>" object
    And response pager should display page <page_number> with <per_page> elements
    And response pager data should be consistent

  Examples:
    | resource                                           | format | page_number | per_page |
    | /api/ecommerce/orders.xml                          | xml    | 1           | 10       |
    | /api/ecommerce/orders.xml?page=1&count=5           | xml    | 1           | 5        |
    | /api/ecommerce/orders.json                         | json   | 1           | 10       |
    | /api/ecommerce/orders.json?page=1&count=5          | json   | 1           | 5        |

  @api @order @id
  Scenario Outline: Retrieve an order by an unique ID
    When I send a GET request to "<resource>"
    Then the response code should be <status_code>
    And response should contain "<format>" object
    And response should contain "<message>"

    Examples:
      | resource| status_code | format | message |
      | /api/ecommerce/orders/1.json | 200 | json | payment_method |
      | /api/ecommerce/orders/1.xml  | 200 | xml  | payment_method |

  @api @order @unknown
  Scenario Outline: Check unavailable unique order
    When I send a GET request to "<resource>"
    Then the response code should be <status_code>
    And response should contain "<format>" object
    And response should contain "<message>"

  Examples:
    | resource                              | status_code | format | message                      |
    | /api/ecommerce/orders/9999999999.json | 404         | json   | Order (9999999999) not found |
    | /api/ecommerce/orders/9999999999.xml  | 404         | xml    | Order (9999999999) not found |


  # ORDER ELEMENTS

  @api @order @order_element @list
  Scenario Outline: Retrieve all order elements of an unique order
    When I send a GET request to "<resource>"
    Then the response code should be <status_code>
    And response should contain "<format>" object
    And response should contain "<message>"

    Examples:
      | resource| status_code | format | message |
      | /api/ecommerce/orders/1/orderelements.json | 200 | json | raw_product |
      | /api/ecommerce/orders/1/orderelements.xml  | 200 | xml  | raw_product |

  @api @order @order_element @unknown
  Scenario Outline: Check order elements of an unavailable order
    When I send a GET request to "<resource>"
    Then the response code should be <status_code>
    And response should contain "<format>" object
    And response should contain "<message>"

  Examples:
    | resource                                              | status_code | format | message                      |
    | /api/ecommerce/orders/9999999999/orderelements.json | 404         | json   | Order (9999999999) not found |
    | /api/ecommerce/orders/9999999999/orderelements.xml  | 404         | xml    | Order (9999999999) not found |
