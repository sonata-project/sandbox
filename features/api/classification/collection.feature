@api @post @classification
Feature: Check the API for ClassificationBundle
  I want to test the API calls about collection


  Background:
    Given I am authenticating as "admin" with "admin" password

  # GET

  Scenario: Get all collections
    When I send a GET request to "/api/classification/collections.xml"
    Then the response code should be 200
    And response should contain "xml" object
    And response should contain "PHP Fan"
    And response should contain "Travels"

  # POST

  Scenario: Post new collection (with errors)
    When I send a POST request to "/api/classification/collections.xml" with values:
      | description | My description |
      | enabled     | 1              |
    Then the response code should be 400
    And response should contain "xml" object
    And response should contain "Validation Failed"
    And response should contain "This value should not be blank"

  Scenario: Collection full workflow
    When I send a POST request to "/api/classification/collections.xml" with values:
      | name        | My collection  |
      | slug        | my-collection  |
      | description | My description |
      | enabled     | 1              |
    Then the response code should be 200
    And response should contain "xml" object
    And response should contain "created_at"
    And store the XML response identifier as "collection_id"

    When I send a GET request to "/api/classification/collections/<collection_id>.xml" using last identifier:
    Then the response code should be 200
    And response should contain "xml" object
    And response should contain "My collection"
    And response should contain "my-collection"
    And response should contain "My description"

    # PUT

    When  I send a PUT request to "/api/classification/collections/<collection_id>.xml" using last identifier with values:
      | name        | My new collection name |
      | slug        | my-new-collection      |
      | description | My new description     |
      | enabled     | 1                      |
    Then the response code should be 200
    And response should contain "xml" object
    And response should contain "My new collection name"
    And response should contain "my-new-collection"
    And response should contain "My new description"

    When I send a GET request to "/api/classification/collections/<collection_id>.xml" using last identifier:
    Then the response code should be 200
    And response should contain "xml" object
    And response should contain "My new collection name"
    And response should contain "my-new-collection"
    And response should contain "My new description"

    # DELETE

    When I send a DELETE request to "/api/classification/collections/<collection_id>.xml" using last identifier:
    Then the response code should be 200
    And response should contain "xml" object
    And response should contain "true"

    When I send a GET request to "/api/classification/collections/<collection_id>.xml" using last identifier:
    Then the response code should be 404
    And response should contain "xml" object