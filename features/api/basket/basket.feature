@api @ecommerce @basket
Feature: Check the API for BasketBundle
  I want to test the API calls

  # POST (basket)

  @api @basket @new
  Scenario: Post new basket (with errors)
    Given I am authenticating as "admin" with "admin" password
    When I send a POST request to "/api/ecommerce/baskets.xml"
    Then the response code should be 500
    And response should contain "xml" object

  @api @basket @workflow
  Scenario: Basket full workflow
    Given I am authenticating as "admin" with "admin" password
    When I send a POST request to "/api/ecommerce/baskets.xml" with values:
      | paymentMethodCode| DEBUG |
      | currency         | EUR |
      | locale           | fr |
    Then the response code should be 200
    And response should contain "xml" object
    And response should contain "cpt_element"
    And response should contain "payment_method_code"
    And response should contain "currency"
    And response should contain "locale"
    And response should contain "fr"
    And response should contain "DEBUG"
    And response should contain "EUR"
    Then  store the XML response identifier as "basket_id"

    Given I am authenticating as "admin" with "admin" password
    When I send a GET request to "/api/ecommerce/baskets/<basket_id>.xml" using last identifier:
    Then the response code should be 200
    And response should contain "xml" object
    And response should contain "cpt_element"
    And response should contain "payment_method_code"
    And response should contain "currency"
    And response should contain "locale"
    And response should contain "fr"
    And response should contain "DEBUG"
    And response should contain "EUR"

    # PUT (basket)

    Given I am authenticating as "admin" with "admin" password
    When  I send a PUT request to "/api/ecommerce/baskets/<basket_id>.xml" using last identifier with values:
      | paymentMethodCode| PAYPAL |
      | currency         | GBP    |
      | locale           | uk     |
    Then the response code should be 200
    And response should contain "xml" object
    And response should contain "cpt_element"
    And response should contain "payment_method_code"
    And response should contain "currency"
    And response should contain "locale"
    And response should contain "uk"
    And response should contain "PAYPAL"
    And response should contain "GBP"

    Given I am authenticating as "admin" with "admin" password
    When I send a GET request to "/api/ecommerce/baskets/<basket_id>.xml" using last identifier:
    Then the response code should be 200
    And response should contain "xml" object
    And response should contain "cpt_element"
    And response should contain "payment_method_code"
    And response should contain "currency"
    And response should contain "locale"
    And response should contain "uk"
    And response should contain "PAYPAL"
    And response should contain "GBP"

    # POST (basket elements)

    Given I am authenticating as "admin" with "admin" password
    When I send a POST request to "/api/ecommerce/baskets/<basket_id>/basketelements.xml" using last identifier with values:
      | productId | 1 |
      | quantity  | 2 |
      | position  | 1 |
    Then  the response code should be 200
    And response should contain "xml" object
    And response should contain "Dummy 1"
    And response should contain "2"
    And response should contain "uk"
    And response should contain "GBP"

    Given I am authenticating as "admin" with "admin" password
    When I send a GET request to "/api/ecommerce/baskets/<basket_id>/basketelements.xml" using last identifier:
    Then the response code should be 200
    And response should contain "xml" object
    And response should contain "Dummy 1"
    And response should contain "2"
    And store the XML response identifier as "element_id"

    Given I am authenticating as "admin" with "admin" password
    When I send a PUT request to "/api/ecommerce/baskets/<basket_id>/basketelements/<element_id>.xml" using last identifier with values:
      | productId | 2 |
      | quantity  | 6 |
      | position  | 1 |
    Then the response code should be 200
    And response should contain "xml" object
    And response should contain "Dummy 2"
    And response should contain "6"
    And response should contain "uk"
    And response should contain "GBP"

    Given I am authenticating as "admin" with "admin" password
    When I send a GET request to "/api/ecommerce/baskets/<basket_id>/basketelements.xml" using last identifier:
    Then the response code should be 200
    And response should contain "xml" object
    And response should contain "Dummy 2"
    And response should contain "6"

    # DELETE (basket elements)

    Given I am authenticating as "admin" with "admin" password
    When I send a DELETE request to "/api/ecommerce/baskets/<basket_id>/basketelements/<element_id>.xml" using last identifier:
    Then the response code should be 200
    And response should contain "xml" object
    And response should contain "cpt_element"
    And response should contain "0"

    # GET (basket)

    Given I am authenticating as "admin" with "admin" password
    When I send a GET request to "/api/ecommerce/baskets.xml"
    Then the response code should be 200
    And response should contain "xml" object
    And response should contain "cpt_element"
    And response should contain "currency"

    # DELETE (basket)

    Given I am authenticating as "admin" with "admin" password
    When I send a DELETE request to "/api/ecommerce/baskets/<basket_id>.xml" using last identifier:
    Then the response code should be 200
    And response should contain "xml" object
    And response should contain "true"

    Given I am authenticating as "admin" with "admin" password
    When I send a GET request to "/api/ecommerce/baskets/<basket_id>.xml" using last identifier:
    Then the response code should be 404
    And response should contain "xml" object