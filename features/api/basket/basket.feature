@api @ecommerce @basket
Feature: Check the Basket controller calls for BasketBundle

  Background:
    Given I am authenticating as "admin" with "admin" password

  # GET

  @api @basket @list
  Scenario Outline: Retrieve all available baskets
    When I send a GET request to "<resource>"
    Then the response code should be <status_code>
    And response should contain "<format>" object
    And response pager should display page <page_number> with <per_page> elements
    And response pager data should be consistent

  Examples:
    | resource                                            | status_code  | format | page_number | per_page |
    | /api/ecommerce/baskets.xml                          | 200          | xml    | 1           | 10       |
    | /api/ecommerce/baskets.xml?page=1&count=5           | 200          | xml    | 1           | 5        |
    | /api/ecommerce/baskets.json                         | 200          | json   | 1           | 10       |
    | /api/ecommerce/baskets.json?page=1&count=5          | 200          | json   | 1           | 5        |


  @api @basket @unknown
  Scenario Outline: Check unavailable unique basket
    When I send a GET request to "<resource>"
    Then the response code should be <status_code>
    And response should contain "<format>" object
    And response should contain "<message>"

  Examples:
    | resource                               | status_code | format | message                       |
    | /api/ecommerce/baskets/9999999999.json | 404         | json   | Basket (9999999999) not found |
    | /api/ecommerce/baskets/9999999999.xml  | 404         | xml    | Basket (9999999999) not found |

  # POST (basket)

  @api @basket @new @ko
  Scenario Outline: Post new basket (with errors)
    When I send a POST request to "/api/ecommerce/baskets.<format>"
    Then the response code should be <status_code>
    And response should contain "<format>" object
    And response should contain "This value should not be null"

  Examples:
    | format  | status_code   |
    | xml     | 400           |
    | json    | 400           |

  @api @basket @workflow
  Scenario Outline: Basket full workflow
    When I send a POST request to "/api/ecommerce/baskets.<format>" with values:
      | paymentMethodCode  | DEBUG |
      | deliveryMethodCode | DEBUG |
      | currency           | EUR   |
      | locale             | fr    |
    Then the response code should be 200
    And response should contain "<format>" object
    And response should contain "cpt_element"
    And response should contain "payment_method_code"
    And response should contain "currency"
    And response should contain "locale"
    And response should contain "fr"
    And response should contain "DEBUG"
    And response should contain "EUR"
    Then  store the <format> response identifier as "basket_id"

    When I send a GET request to "/api/ecommerce/baskets/<basket_id>.<format>" using last identifier:
    Then the response code should be 200
    And response should contain "<format>" object
    And response should contain "cpt_element"
    And response should contain "payment_method_code"
    And response should contain "currency"
    And response should contain "locale"
    And response should contain "fr"
    And response should contain "DEBUG"
    And response should contain "EUR"

    # PUT (basket)

    When  I send a PUT request to "/api/ecommerce/baskets/<basket_id>.<format>" using last identifier with values:
      | paymentMethodCode   | PAYPAL |
      | deliveryMethodCode  | free   |
      | currency            | GBP    |
      | locale              | uk     |
    Then the response code should be 200
    And response should contain "<format>" object
    And response should contain "cpt_element"
    And response should contain "payment_method_code"
    And response should contain "currency"
    And response should contain "locale"
    And response should contain "uk"
    And response should contain "PAYPAL"
    And response should contain "GBP"

    When I send a GET request to "/api/ecommerce/baskets/<basket_id>.<format>" using last identifier:
    Then the response code should be 200
    And response should contain "<format>" object
    And response should contain "cpt_element"
    And response should contain "payment_method_code"
    And response should contain "currency"
    And response should contain "locale"
    And response should contain "uk"
    And response should contain "PAYPAL"
    And response should contain "GBP"

    # POST (basket elements)
    When I send a POST request to "/api/ecommerce/baskets/<basket_id>/basketelements.<format>" using last identifier with values:
      | productId | 1 |
      | quantity  | 2 |
      | position  | 1 |
    Then  the response code should be 200
    And response should contain "<format>" object
    And response should contain "Dummy 1"
    And response should contain "2"
    And response should contain "uk"
    And response should contain "GBP"
    And store the <format> response basketelement identifier as "element_id"

    When I send a GET request to "/api/ecommerce/baskets/<basket_id>/basketelements.<format>" using last identifier:
    Then the response code should be 200
    And response should contain "<format>" object
    And response should contain "Dummy 1"
    And response should contain "2"

    When I send a PUT request to "/api/ecommerce/baskets/<basket_id>/basketelements/<element_id>.<format>" using last identifier with values:
      | productId | 2 |
      | quantity  | 6 |
      | position  | 1 |
    Then the response code should be 200
    And response should contain "<format>" object
    And response should contain "Dummy 2"
    And response should contain "6"
    And response should contain "uk"
    And response should contain "GBP"

    When I send a GET request to "/api/ecommerce/baskets/<basket_id>/basketelements.<format>" using last identifier:
    Then the response code should be 200
    And response should contain "<format>" object
    And response should contain "Dummy 2"
    And response should contain "6"

    # DELETE (basket elements)

    When I send a DELETE request to "/api/ecommerce/baskets/<basket_id>/basketelements/<element_id>.<format>" using last identifier:
    Then the response code should be 200
    And response should contain "<format>" object
    And response should contain "cpt_element"
    And response should contain "0"

    # DELETE (basket)

    When I send a DELETE request to "/api/ecommerce/baskets/<basket_id>.<format>" using last identifier:
    Then the response code should be 200
    And response should contain "<format>" object
    And response should contain "true"

    When I send a GET request to "/api/ecommerce/baskets/<basket_id>.<format>" using last identifier:
    Then the response code should be 404
    And response should contain "<format>" object

  Examples:
    | format  |
    | xml     |
    | json    |
