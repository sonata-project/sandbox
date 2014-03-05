@api @post @classification
Feature: Check the Tag controller calls for ClassificationBundle

  # GET

  Scenario: Get all tags
    Given I am authenticating as "admin" with "admin" password
    When  I send a GET request to "/api/classification/tags.xml"
    Then  the response code should be 200
    And   the response should contain XML
    And   response should contain "form"
    And   response should contain "general"

  # POST

  Scenario: Post new tag (with errors)
    Given I am authenticating as "admin" with "admin" password
    When  I send a POST request to "/api/classification/tags.xml" with values:
      | enabled     | 1         |
    Then  the response code should be 400
    And   the response should contain XML
    And   response should contain "Validation Failed"
    And   response should contain "This value should not be blank"

  Scenario: Tag full workflow
    Given I am authenticating as "admin" with "admin" password
    When  I send a POST request to "/api/classification/tags.xml" with values:
      | name        | My tag    |
      | slug        | my-tag    |
      | enabled     | 1         |
    Then  the response code should be 200
    And   the response should contain XML
    Then  response should contain "created_at"
    Then  store the XML response identifier as "tag_id"

    Given I am authenticating as "admin" with "admin" password
    When  I send a GET request to "/api/classification/tags/<tag_id>.xml" using last identifier:
    Then  the response code should be 200
    And   the response should contain XML
    And   response should contain "My tag"
    And   response should contain "my-tag"

    # PUT

    Given I am authenticating as "admin" with "admin" password
    When  I send a PUT request to "/api/classification/tags/<tag_id>.xml" using last identifier with values:
      | name        | My new tag name |
      | slug        | my-new-tag      |
      | enabled     | 1               |
    Then  the response code should be 200
    And   the response should contain XML
    Then  response should contain "My new tag name"
    And   response should contain "my-new-tag"

    Given I am authenticating as "admin" with "admin" password
    When  I send a GET request to "/api/classification/tags/<tag_id>.xml" using last identifier:
    Then  the response code should be 200
    And   the response should contain XML
    And   response should contain "My new tag name"
    And   response should contain "my-new-tag"

    # DELETE

    Given I am authenticating as "admin" with "admin" password
    When  I send a DELETE request to "/api/classification/tags/<tag_id>.xml" using last identifier:
    Then  the response code should be 200
    And   the response should contain XML
    Then  response should contain "true"

    Given I am authenticating as "admin" with "admin" password
    When  I send a GET request to "/api/classification/tags/<tag_id>.xml" using last identifier:
    Then  the response code should be 404
    And   the response should contain XML