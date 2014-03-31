@api @post @classification
Feature: Check the API for ClassificationBundle
  I want to test the API calls about tag

  Background:
    Given I am authenticating as "admin" with "admin" password

  # GET

  Scenario: Get all tags
    When I send a GET request to "/api/classification/tags.xml"
    Then the response code should be 200
    And response should contain "xml" object
    And response should contain "form"
    And response should contain "general"

  # POST

  Scenario: Post new tag (with errors)
    When I send a POST request to "/api/classification/tags.xml" with values:
      | enabled     | 1         |
    Then the response code should be 400
    And response should contain "xml" object
    And response should contain "Validation Failed"
    And response should contain "This value should not be blank"

  Scenario: Tag full workflow
    When I send a POST request to "/api/classification/tags.xml" with values:
      | name        | My tag    |
      | slug        | my-tag    |
      | enabled     | 1         |
    Then  the response code should be 200
    And response should contain "xml" object
    And response should contain "created_at"
    And store the XML response identifier as "tag_id"

    When I send a GET request to "/api/classification/tags/<tag_id>.xml" using last identifier:
    Then the response code should be 200
    And response should contain "xml" object
    And response should contain "My tag"
    And response should contain "my-tag"

    # PUT

    When I send a PUT request to "/api/classification/tags/<tag_id>.xml" using last identifier with values:
      | name        | My new tag name |
      | slug        | my-new-tag      |
      | enabled     | 1               |
    Then the response code should be 200
    And response should contain "xml" object
    And response should contain "My new tag name"
    And response should contain "my-new-tag"

    When I send a GET request to "/api/classification/tags/<tag_id>.xml" using last identifier:
    Then the response code should be 200
    And response should contain "xml" object
    And response should contain "My new tag name"
    And response should contain "my-new-tag"

    # DELETE

    When I send a DELETE request to "/api/classification/tags/<tag_id>.xml" using last identifier:
    Then the response code should be 200
    And response should contain "xml" object
    And response should contain "true"

    When I send a GET request to "/api/classification/tags/<tag_id>.xml" using last identifier:
    Then the response code should be 404
    And response should contain "xml" object