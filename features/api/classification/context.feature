@api @classification @context
Feature: Check the API for ClassificationBundle
  I want to test the API calls about tag

  Background:
    Given I am authenticating as "admin" with "admin" password

  # GET

  @api @classification @context @list
  Scenario Outline: Get all contexts
    When I send a GET request to "<resource>"
    Then the response code should be 200
    And response should contain "<format>" object
    And response pager should display page <page_number> with <per_page> elements
    And response pager data should be consistent

  Examples:
    | resource                                                  | format | page_number | per_page |
    | /api/classification/contexts.xml                          | xml    | 1           | 10       |
    | /api/classification/contexts.xml?page=1&count=5           | xml    | 1           | 5        |
    | /api/classification/contexts.json                         | json   | 1           | 10       |
    | /api/classification/contexts.json?page=1&count=5          | json   | 1           | 5        |

  @api @classification @context @unknown
  Scenario Outline: Get a specific context that not exists
    When I send a GET request to "/api/classification/contexts/99999999999.<format>"
    Then the response code should be 404
    And response should contain "<format>" object
    And response should contain "Context (99999999999) not found"

  Examples:
    | format  |
    | xml     |
    | json    |

  # POST

  @api @classification @context @new @ko
  Scenario Outline: Post new context (with errors)
    When I send a POST request to "/api/classification/contexts.<format>" with values:
      | enabled     | 1         |
    Then the response code should be 400
    And response should contain "<format>" object
    And response should contain "Validation Failed"
    And response should contain "This value should not be blank"

  Examples:
    | format  |
    | xml     |
    | json    |

  @api @classification @context @workflow
  Scenario Outline: Context full workflow
    When I send a POST request to "/api/classification/contexts.<format>" with values:
      | name        | My context |
      | enabled     | 1          |
      | id          | my_context |
    Then  the response code should be 200
    And response should contain "<format>" object
    And response should contain "created_at"
    And store the <format> response identifier as "context_id"

    When I send a GET request to "/api/classification/contexts/<context_id>.<format>" using last identifier:
    Then the response code should be 200
    And response should contain "<format>" object
    And response should contain "My context"

    # PUT

    When I send a PUT request to "/api/classification/contexts/<context_id>.<format>" using last identifier with values:
      | name        | My new context name |
      | enabled     | 1                   |
      | id          | my_context          |
    Then the response code should be 200
    And response should contain "<format>" object
    And response should contain "My new context name"
    And response should contain "my_context"

    When I send a GET request to "/api/classification/contexts/<context_id>.<format>" using last identifier:
    Then the response code should be 200
    And response should contain "<format>" object
    And response should contain "My new context name"
    And response should contain "my_context"

    # DELETE

    When I send a DELETE request to "/api/classification/contexts/<context_id>.<format>" using last identifier:
    Then the response code should be 200
    And response should contain "<format>" object
    And response should contain "true"

    When I send a GET request to "/api/classification/contexts/<context_id>.<format>" using last identifier:
    Then the response code should be 404
    And response should contain "<format>" object

  Examples:
    | format  |
    | xml     |
    | json    |
