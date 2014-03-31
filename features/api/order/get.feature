@api @get @order @ecommerce
Feature: Check the GET API calls for OrderBundle

  Background:
    Given I am authenticating as "admin" with "admin" password

  Scenario Outline: Retrieve the list of orders
    When I send a GET request to "<resource>"
    Then the response code should be <status_code>
    And response should contain "<format>" object
    And response should contain "<message>"

    Examples:
      | resource| status_code | format | message |
      | /api/ecommerce/orders.json | 200 | json | payment_method |
      | /api/ecommerce/orders.xml  | 200 | xml  | payment_method |

  Scenario Outline: Retrieve an order by an unique ID
    When I send a GET request to "<resource>"
    Then the response code should be <status_code>
    And response should contain "<format>" object
    And response should contain "<message>"

    Examples:
      | resource| status_code | format | message |
      | /api/ecommerce/orders/1.json | 200 | json | payment_method |
      | /api/ecommerce/orders/1.xml  | 200 | xml  | payment_method |

  Scenario Outline: Retrieve all order elements of an unique order
    When I send a GET request to "<resource>"
    Then the response code should be <status_code>
    And response should contain "<format>" object
    And response should contain "<message>"

    Examples:
      | resource| status_code | format | message |
      | /api/ecommerce/orders/1/orderelements.json | 200 | json | raw_product |
      | /api/ecommerce/orders/1/orderelements.xml  | 200 | xml  | raw_product |