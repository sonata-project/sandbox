@api @ecommerce @customer
Feature: Check the Customer controller calls for CustomerBundle
  I want to test the API calls

  Background:
    Given I am authenticating as "admin" with "admin" password

  # GET

  @api @ecommerce @customer @list
  Scenario Outline: Get all customers
    When I send a GET request to "<resource>"
    Then the response code should be 200
    And response should contain "<format>" object
    And response pager should display page <page_number> with <per_page> elements
    And response pager data should be consistent

  Examples:
    | resource                                              | format | page_number | per_page |
    | /api/ecommerce/customers.xml                          | xml    | 1           | 10       |
    | /api/ecommerce/customers.xml?page=2&count=5           | xml    | 2           | 5        |
    | /api/ecommerce/customers.json                         | json   | 1           | 10       |
    | /api/ecommerce/customers.json?page=2&count=5          | json   | 2           | 5        |


  @api @ecommerce @customer @unknown
  Scenario Outline: Get a specific customer that not exists
    When I send a GET request to "/api/ecommerce/customers/99999999999.<format>"
    Then the response code should be 404
    And response should contain "<format>" object
    And response should contain "Customer (99999999999) not found"

  Examples:
    | format  |
    | xml     |
    | json    |

  # POST

  @api @ecommerce @customer @new @ko
  Scenario Outline: Post new customer (with errors)
    When I send a POST request to "/api/ecommerce/customers.<format>" with values:
      | title     | 1                  |
      | user      | 1                  |
    Then the response code should be 400
    And response should contain "<format>" object
    And response should contain "Validation Failed"
    And response should contain "This value should not be null"

  Examples:
    | format  |
    | xml     |
    | json    |

  @api @ecommerce @customer @workflow
  Scenario Outline: Tag full workflow
    When I send a POST request to "/api/ecommerce/customers.<format>" with values:
      | title     | 1                  |
      | firstname | My firstname       |
      | lastname  | My lastname        |
      | email     | customer@email.com |
      | user      | 1                  |
    Then the response code should be 200
    And response should contain "<format>" object
    And response should contain "created_at"
    And store the <format> response identifier as "customer_id"

    When I send a GET request to "/api/ecommerce/customers/<customer_id>.<format>" using last identifier:
    Then the response code should be 200
    And response should contain "<format>" object
    And response should contain "My firstname"
    And response should contain "My lastname"
    And response should contain "customer@email.com"

    # PUT

    When I send a PUT request to "/api/ecommerce/customers/<customer_id>.<format>" using last identifier with values:
      | title     | 1                  |
      | firstname | My new firstname   |
      | lastname  | My new lastname    |
      | email     | customer@email.com |
      | user      | 1                  |
    Then the response code should be 200
    And response should contain "<format>" object
    Then response should contain "My new firstname"
    And response should contain "My new lastname"
    And response should contain "customer@email.com"

    When I send a GET request to "/api/ecommerce/customers/<customer_id>.<format>" using last identifier:
    Then the response code should be 200
    And response should contain "<format>" object
    And response should contain "My new firstname"
    And response should contain "My new lastname"
    And response should contain "customer@email.com"

    # ADDRESS

    When  I send a POST request to "/api/ecommerce/customers/<customer_id>/addresses.<format>" using last identifier with values:
      | name        | My custom address |
      | type        | 1                 |
      | firstname   | My firstname      |
      | lastname    | My lastname       |
      | address1    | 1 rue du test     |
      | postcode    | 75000             |
      | city        | Paris             |
      | countryCode | FR                |
    Then the response code should be 200
    And response should contain "<format>" object
    Then response should contain "created_at"
    Then store the <format> response identifier as "address_id"

    When I send a GET request to "/api/ecommerce/addresses/<address_id>.<format>" using last identifier:
    Then the response code should be 200
    And response should contain "<format>" object
    And response should contain "My custom address"
    And response should contain "My firstname"
    And response should contain "My lastname"
    And response should contain "1 rue du test"
    And response should contain "Paris"
    And response should contain "FR"

    When I send a PUT request to "/api/ecommerce/addresses/<address_id>.<format>" using last identifier with values:
      | name        | My custom address |
      | type        | 1                 |
      | firstname   | My new firstname  |
      | lastname    | My new lastname   |
      | address1    | 1 rue du test     |
      | postcode    | 75000             |
      | city        | Paris             |
      | countryCode | FR                |
      | customer    | 1                 |
    Then the response code should be 200
    And response should contain "<format>" object
    Then response should contain "created_at"
    Then store the <format> response identifier as "address_id"

    When I send a GET request to "/api/ecommerce/addresses/<address_id>.<format>" using last identifier:
    Then the response code should be 200
    And response should contain "<format>" object
    And response should contain "My custom address"
    And response should contain "My new firstname"
    And response should contain "My new lastname"
    And response should contain "1 rue du test"
    And response should contain "Paris"
    And response should contain "FR"

    When I send a DELETE request to "/api/ecommerce/addresses/<address_id>.<format>" using last identifier:
    Then the response code should be 200
    And response should contain "<format>" object
    And response should contain "true"

    When I send a GET request to "/api/ecommerce/addresses/<address_id>.<format>" using last identifier:
    Then the response code should be 404
    And response should contain "<format>" object

    # DELETE

    When I send a DELETE request to "/api/ecommerce/customers/<customer_id>.<format>" using last identifier:
    Then the response code should be 200
    And response should contain "<format>" object
    And response should contain "true"

    When I send a GET request to "/api/ecommerce/customers/<customer_id>.<format>" using last identifier:
    Then the response code should be 404
    And response should contain "<format>" object

  Examples:
    | format  |
    | xml     |
    | json    |
