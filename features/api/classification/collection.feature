@api @post @classification
Feature: Check the Collection controller calls for ClassificationBundle

  # GET

  Scenario: Get all collections
    Given I am authenticating as "admin" with "admin" password
    When  I send a GET request to "/api/classification/collections.xml"
    Then  the response code should be 200
    And   the response should contain XML
    And   response should contain "PHP Fan"
    And   response should contain "Travels"

  # POST

  Scenario: Post new collection (with errors)
    Given I am authenticating as "admin" with "admin" password
    When  I send a POST request to "/api/classification/collections.xml" with values:
      | description | My description |
      | enabled     | 1              |
    Then  the response code should be 400
    And   the response should contain XML
    And   response should contain "Validation Failed"
    And   response should contain "This value should not be blank"

  Scenario: Collection full workflow
    Given I am authenticating as "admin" with "admin" password
    When  I send a POST request to "/api/classification/collections.xml" with values:
      | name        | My collection  |
      | slug        | my-collection  |
      | description | My description |
      | enabled     | 1              |
    Then  the response code should be 200
    And   the response should contain XML
    Then  response should contain "created_at"
    Then  store the XML response identifier as "collection_id"

    Given I am authenticating as "admin" with "admin" password
    When  I send a GET request to "/api/classification/collections/<collection_id>.xml" using last identifier:
    Then  the response code should be 200
    And   the response should contain XML
    And   response should contain "My collection"
    And   response should contain "my-collection"
    And   response should contain "My description"

    # PUT

    Given I am authenticating as "admin" with "admin" password
    When  I send a PUT request to "/api/classification/collections/<collection_id>.xml" using last identifier with values:
      | name        | My new collection name |
      | slug        | my-new-collection      |
      | description | My new description     |
      | enabled     | 1                      |
    Then  the response code should be 200
    And   the response should contain XML
    Then  response should contain "My new collection name"
    And   response should contain "my-new-collection"
    And   response should contain "My new description"

    Given I am authenticating as "admin" with "admin" password
    When  I send a GET request to "/api/classification/collections/<collection_id>.xml" using last identifier:
    Then  the response code should be 200
    And   the response should contain XML
    And   response should contain "My new collection name"
    And   response should contain "my-new-collection"
    And   response should contain "My new description"

    # DELETE

    Given I am authenticating as "admin" with "admin" password
    When  I send a DELETE request to "/api/classification/collections/<collection_id>.xml" using last identifier:
    Then  the response code should be 200
    And   the response should contain XML
    Then  response should contain "true"

    Given I am authenticating as "admin" with "admin" password
    When  I send a GET request to "/api/classification/collections/<collection_id>.xml" using last identifier:
    Then  the response code should be 404
    And   the response should contain XML