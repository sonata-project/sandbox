@api @ecommerce @customer
Feature: Check the Customer controller calls for CustomerBundle

  # GET

  Scenario: Get all customers
    Given I am authenticating as "admin" with "admin" password
    When  I send a GET request to "/api/ecommerce/customers.xml"
    Then  the response code should be 200
    And   the response should contain XML

  # POST

  Scenario: Post new customer (with errors)
    Given I am authenticating as "admin" with "admin" password
    When  I send a POST request to "/api/ecommerce/customers.xml" with values:
      | title     | 1                  |
      | user      | 1                  |
    Then  the response code should be 400
    And   the response should contain XML
    And   response should contain "Validation Failed"
    And   response should contain "This value should not be null"

  Scenario: Tag full workflow
    Given I am authenticating as "admin" with "admin" password
    When  I send a POST request to "/api/ecommerce/customers.xml" with values:
      | title     | 1                  |
      | firstname | My firstname       |
      | lastname  | My lastname        |
      | email     | customer@email.com |
      | user      | 1                  |
    Then  the response code should be 200
    And   the response should contain XML
    Then  response should contain "created_at"
    Then  store the XML response identifier as "customer_id"

    Given I am authenticating as "admin" with "admin" password
    When  I send a GET request to "/api/ecommerce/customers/<customer_id>.xml" using last identifier:
    Then  the response code should be 200
    And   the response should contain XML
    And   response should contain "My firstname"
    And   response should contain "My lastname"
    And   response should contain "customer@email.com"

    # PUT

    Given I am authenticating as "admin" with "admin" password
    When  I send a PUT request to "/api/ecommerce/customers/<customer_id>.xml" using last identifier with values:
      | title     | 1                  |
      | firstname | My new firstname   |
      | lastname  | My new lastname    |
      | email     | customer@email.com |
      | user      | 1                  |
    Then  the response code should be 200
    And   the response should contain XML
    Then  response should contain "My new firstname"
    And   response should contain "My new lastname"
    And   response should contain "customer@email.com"

    Given I am authenticating as "admin" with "admin" password
    When  I send a GET request to "/api/ecommerce/customers/<customer_id>.xml" using last identifier:
    Then  the response code should be 200
    And   the response should contain XML
    And   response should contain "My new firstname"
    And   response should contain "My new lastname"
    And   response should contain "customer@email.com"

    # ADDRESS

    Given I am authenticating as "admin" with "admin" password
    When  I send a POST request to "/api/ecommerce/customers/<customer_id>/addresses.xml" using last identifier with values:
      | name        | My custom address |
      | type        | 1                 |
      | firstname   | My firstname      |
      | lastname    | My lastname       |
      | address1    | 1 rue du test     |
      | postcode    | 75000             |
      | city        | Paris             |
      | countryCode | FR                |
    Then  the response code should be 200
    And   the response should contain XML
    Then  response should contain "created_at"
    Then  store the XML response identifier as "address_id"

    Given I am authenticating as "admin" with "admin" password
    When  I send a GET request to "/api/ecommerce/addresses/<address_id>.xml" using last identifier:
    Then  the response code should be 200
    And   the response should contain XML
    And   response should contain "My custom address"
    And   response should contain "My firstname"
    And   response should contain "My lastname"
    And   response should contain "1 rue du test"
    And   response should contain "Paris"
    And   response should contain "FR"

    Given I am authenticatingson as "admin" with "admin" password
    When  I send a PUT request to "/api/ecommerce/addresses/<address_id>.xml" using last identifier with values:
      | name        | My custom address |
      | type        | 1                 |
      | firstname   | My new firstname  |
      | lastname    | My new lastname   |
      | address1    | 1 rue du test     |
      | postcode    | 75000             |
      | city        | Paris             |
      | countryCode | FR                |
      | customer    | 1                 |
    Then  the response code should be 200
    And   the response should contain XML
    Then  response should contain "created_at"
    Then  store the XML response identifier as "address_id"

    Given I am authenticating as "admin" with "admin" password
    When  I send a GET request to "/api/ecommerce/addresses/<address_id>.xml" using last identifier:
    Then  the response code should be 200
    And   the response should contain XML
    And   response should contain "My custom address"
    And   response should contain "My new firstname"
    And   response should contain "My new lastname"
    And   response should contain "1 rue du test"
    And   response should contain "Paris"
    And   response should contain "FR"

    Given I am authenticating as "admin" with "admin" password
    When  I send a DELETE request to "/api/ecommerce/addresses/<address_id>.xml" using last identifier:
    Then  the response code should be 200
    And   the response should contain XML
    Then  response should contain "true"

    Given I am authenticating as "admin" with "admin" password
    When  I send a GET request to "/api/ecommerce/addresses/<address_id>.xml" using last identifier:
    Then  the response code should be 404
    And   the response should contain XML

    # DELETE

    Given I am authenticating as "admin" with "admin" password
    When  I send a DELETE request to "/api/ecommerce/customers/<customer_id>.xml" using last identifier:
    Then  the response code should be 200
    And   the response should contain XML
    Then  response should contain "true"

    Given I am authenticating as "admin" with "admin" password
    When  I send a GET request to "/api/ecommerce/customers/<customer_id>.xml" using last identifier:
    Then  the response code should be 404
    And   the response should contain XML