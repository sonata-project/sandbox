@api @post @classification
Feature: Check the Category controller calls for ClassificationBundle

  # GET

  Scenario: Get all categories
    Given I am authenticating as "admin" with "admin" password
    When  I send a GET request to "/api/classification/categories.xml"
    Then  the response code should be 200
    And   the response should contain XML
    And   response should contain "Goodies"
    And   response should contain "Plushes"

  # POST

  Scenario: Post new category (with errors)
    Given I am authenticating as "admin" with "admin" password
    When  I send a POST request to "/api/classification/categories.xml" with values:
      | description | My description |
      | enabled     | 1              |
      | position    | 1              |
    Then  the response code should be 400
    And   the response should contain XML
    And   response should contain "Validation Failed"
    And   response should contain "This value should not be blank"

  Scenario: Category full workflow
    Given I am authenticating as "admin" with "admin" password
    When  I send a POST request to "/api/classification/categories.xml" with values:
      | name        | My category    |
      | slug        | my-category    |
      | description | My description |
      | enabled     | 1              |
      | position    | 1              |
    Then  the response code should be 200
    And   the response should contain XML
    Then  response should contain "created_at"
    Then  store the XML response identifier as "category_id"

    Given I am authenticating as "admin" with "admin" password
    When  I send a GET request to "/api/classification/categories/<category_id>.xml" using last identifier:
    Then  the response code should be 200
    And   the response should contain XML
    And   response should contain "My category"
    And   response should contain "my-category"
    And   response should contain "My description"

    # PUT

    Given I am authenticating as "admin" with "admin" password
    When  I send a PUT request to "/api/classification/categories/<category_id>.xml" using last identifier with values:
      | name        | My new category name |
      | slug        | my-new-category      |
      | description | My new description   |
      | enabled     | 1                    |
      | position    | 1                    |
    Then  the response code should be 200
    And   the response should contain XML
    Then  response should contain "My new category name"
    And   response should contain "my-new-category"
    And   response should contain "My new description"

    Given I am authenticating as "admin" with "admin" password
    When  I send a GET request to "/api/classification/categories/<category_id>.xml" using last identifier:
    Then  the response code should be 200
    And   the response should contain XML
    And   response should contain "My new category name"
    And   response should contain "my-new-category"
    And   response should contain "My new description"

    # DELETE

    Given I am authenticating as "admin" with "admin" password
    When  I send a DELETE request to "/api/classification/categories/<category_id>.xml" using last identifier:
    Then  the response code should be 200
    And   the response should contain XML
    Then  response should contain "true"

    Given I am authenticating as "admin" with "admin" password
    When  I send a GET request to "/api/classification/categories/<category_id>.xml" using last identifier:
    Then  the response code should be 404
    And   the response should contain XML