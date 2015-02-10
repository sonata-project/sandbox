@api @post @classification @context
Feature: Check the API for ClassificationBundle
  I want to test the API calls about tag

  Background:
    Given I am authenticating as "admin" with "admin" password

  # GET

  Scenario: Get all contexts
    When I send a GET request to "/api/classification/contexts.xml"
    Then the response code should be 200
    And response should contain "xml" object
    And response should contain "page"
    And response should contain "entries"

  # POST

  Scenario: Post new context (with errors)
    When I send a POST request to "/api/classification/contexts.xml" with values:
      | enabled     | 1         |
    Then the response code should be 400
    And response should contain "xml" object
    And response should contain "Validation Failed"
    And response should contain "This value should not be blank"

  Scenario: Context full workflow
    When I send a POST request to "/api/classification/contexts.xml" with values:
      | name        | My context |
      | enabled     | 1          |
      | id          | my_context |
    Then  the response code should be 200
    And response should contain "xml" object
    And response should contain "created_at"
    And store the XML response identifier as "context_id"

    When I send a GET request to "/api/classification/contexts/<context_id>.xml" using last identifier:
    Then the response code should be 200
    And response should contain "xml" object
    And response should contain "My context"

    # PUT

    When I send a PUT request to "/api/classification/contexts/<context_id>.xml" using last identifier with values:
      | name        | My new context name |
      | enabled     | 1                   |
      | id          | my_context          |
    Then the response code should be 200
    And response should contain "xml" object
    And response should contain "My new context name"
    And response should contain "my_context"

    When I send a GET request to "/api/classification/contexts/<context_id>.xml" using last identifier:
    Then the response code should be 200
    And response should contain "xml" object
    And response should contain "My new context name"
    And response should contain "my_context"

    # DELETE

    When I send a DELETE request to "/api/classification/contexts/<context_id>.xml" using last identifier:
    Then the response code should be 200
    And response should contain "xml" object
    And response should contain "true"

    When I send a GET request to "/api/classification/contexts/<context_id>.xml" using last identifier:
    Then the response code should be 404
    And response should contain "xml" object
