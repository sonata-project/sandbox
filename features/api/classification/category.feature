@api @post @classification
Feature: Check the API for ClassificationBundle
  I want to test the API calls about category


  Background:
    Given I am authenticating as "admin" with "admin" password

  # GET

  @api @post @classification @category
  Scenario: Get all categories
    When I send a GET request to "/api/classification/categories.xml"
    Then the response code should be 200
    And response should contain "xml" object
    And response should contain "Quebec"
    And response should contain "Switzerland"

  # POST

  Scenario: Post new category (with errors)
    When I send a POST request to "/api/classification/categories.xml" with values:
      | description | My description |
      | enabled     | 1              |
      | position    | 1              |
    Then the response code should be 400
    And response should contain "xml" object
    And response should contain "Validation Failed"
    And response should contain "This value should not be blank"

  Scenario: Category full workflow
    When I send a POST request to "/api/classification/categories.xml" with values:
      | name        | My category    |
      | slug        | my-category    |
      | description | My description |
      | enabled     | 1              |
      | position    | 1              |
    Then the response code should be 200
    And response should contain "xml" object
    And response should contain "created_at"
    And store the XML response identifier as "category_id"

    When I send a GET request to "/api/classification/categories/<category_id>.xml" using last identifier:
    Then the response code should be 200
    And response should contain "xml" object
    And response should contain "My category"
    And response should contain "my-category"
    And response should contain "My description"

    # PUT

    When  I send a PUT request to "/api/classification/categories/<category_id>.xml" using last identifier with values:
      | name        | My new category name |
      | slug        | my-new-category      |
      | description | My new description   |
      | enabled     | 1                    |
      | position    | 1                    |
    Then the response code should be 200
    And response should contain "xml" object
    And response should contain "My new category name"
    And response should contain "my-new-category"
    And response should contain "My new description"

    When I send a GET request to "/api/classification/categories/<category_id>.xml" using last identifier:
    Then the response code should be 200
    And response should contain "xml" object
    And response should contain "My new category name"
    And response should contain "my-new-category"
    And response should contain "My new description"

    # DELETE

    When I send a DELETE request to "/api/classification/categories/<category_id>.xml" using last identifier:
    Then the response code should be 200
    And response should contain "xml" object
    And response should contain "true"

    When I send a GET request to "/api/classification/categories/<category_id>.xml" using last identifier:
    Then the response code should be 404
    And response should contain "xml" object