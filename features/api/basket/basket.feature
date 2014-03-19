@api @ecommerce @basket
Feature: Check the Basket controller calls for BasketBundle

  # POST

  Scenario: Post new basket (with errors)
    Given I am authenticating as "admin" with "admin" password
    When  I send a POST request to "/api/ecommerce/baskets.xml"
    Then  the response code should be 500
    And   the response should contain XML

  Scenario: Basket full workflow
    Given I am authenticating as "admin" with "admin" password
    When  I send a POST request to "/api/ecommerce/baskets.xml" with values:
      | paymentMethodCode| DEBUG |
      | currency         | EUR |
      | locale           | fr |
    Then  the response code should be 200
    And   the response should contain XML
    And   response should contain "cpt_element"
    And   response should contain "payment_method_code"
    And   response should contain "currency"
    And   response should contain "locale"
    And   response should contain "fr"
    And   response should contain "DEBUG"
    And   response should contain "EUR"
    Then  store the XML response identifier as "basket_id"

    Given I am authenticating as "admin" with "admin" password
    When  I send a GET request to "/api/ecommerce/baskets/<basket_id>.xml" using last identifier:
    Then  the response code should be 200
    And   the response should contain XML
    And   response should contain "cpt_element"
    And   response should contain "payment_method_code"
    And   response should contain "currency"
    And   response should contain "locale"
    And   response should contain "fr"
    And   response should contain "DEBUG"
    And   response should contain "EUR"

    # PUT

    Given I am authenticating as "admin" with "admin" password
    When  I send a PUT request to "/api/ecommerce/baskets/<basket_id>.xml" using last identifier with values:
      | paymentMethodCode| PAYPAL |
      | currency         | GBP    |
      | locale           | uk     |
    Then  the response code should be 200
    And   the response should contain XML
    And   response should contain "cpt_element"
    And   response should contain "payment_method_code"
    And   response should contain "currency"
    And   response should contain "locale"
    And   response should contain "uk"
    And   response should contain "PAYPAL"
    And   response should contain "GBP"

    Given I am authenticating as "admin" with "admin" password
    When  I send a GET request to "/api/ecommerce/baskets/<basket_id>.xml" using last identifier:
    Then  the response code should be 200
    And   the response should contain XML
    And   response should contain "cpt_element"
    And   response should contain "payment_method_code"
    And   response should contain "currency"
    And   response should contain "locale"
    And   response should contain "uk"
    And   response should contain "PAYPAL"
    And   response should contain "GBP"

    # GET

    Given I am authenticating as "admin" with "admin" password
    When  I send a GET request to "/api/ecommerce/baskets.xml"
    Then  the response code should be 200
    And   the response should contain XML
    Then  response should contain "cpt_element"
    Then  response should contain "currency"

    # DELETE

    Given I am authenticating as "admin" with "admin" password
    When  I send a DELETE request to "/api/ecommerce/baskets/<basket_id>.xml" using last identifier:
    Then  the response code should be 200
    And   the response should contain XML
    Then  response should contain "true"

    Given I am authenticating as "admin" with "admin" password
    When  I send a GET request to "/api/ecommerce/baskets/<basket_id>.xml" using last identifier:
    Then  the response code should be 404
    And   the response should contain XML