@api @classification @category
Feature: Check the API for ClassificationBundle
  I want to test the API calls about category


  Background:
    Given I am authenticating as "admin" with "admin" password

  # GET

  @api @classification @category @list
  Scenario Outline: Get all categories
    When I send a GET request to "<resource>"
    Then the response code should be 200
    And response should contain "<format>" object
    And response pager should display page <page_number> with <per_page> elements
    And response pager data should be consistent

  Examples:
    | resource                                                    | format | page_number | per_page |
    | /api/classification/categories.xml                          | xml    | 1           | 10       |
    | /api/classification/categories.xml?page=1&count=5           | xml    | 1           | 5        |
    | /api/classification/categories.json                         | json   | 1           | 10       |
    | /api/classification/categories.json?page=1&count=5          | json   | 1           | 5        |

  @api @classification @category @unknown
  Scenario Outline: Get a specific category that not exists
    When I send a GET request to "/api/classification/categories/99999999999.<format>"
    Then the response code should be 404
    And response should contain "<format>" object
    And response should contain "Category (99999999999) not found"

  Examples:
    | format  |
    | xml     |
    | json    |

  # POST

  @api @classification @category @new @ko
  Scenario Outline: Post new category (with errors)
    When I send a POST request to "/api/classification/categories.<format>" with values:
      | description | My description |
      | enabled     | 1              |
      | position    | 1              |
    Then the response code should be 400
    And response should contain "<format>" object
    And response should contain "Validation Failed"
    And response should contain "This value should not be blank"

  Examples:
    | format  |
    | xml     |
    | json    |

  @api @classification @category @workflow
  Scenario Outline: Category full workflow
    When I send a POST request to "/api/classification/categories.<format>" with values:
      | name        | My category    |
      | slug        | my-category    |
      | description | My description |
      | enabled     | 1              |
      | position    | 1              |
      | context     | default        |
    Then the response code should be 200
    And response should contain "<format>" object
    And response should contain "created_at"
    And store the <format> response identifier as "category_id"

    When I send a GET request to "/api/classification/categories/<category_id>.<format>" using last identifier:
    Then the response code should be 200
    And response should contain "<format>" object
    And response should contain "My category"
    And response should contain "my-category"
    And response should contain "My description"

    # PUT

    When  I send a PUT request to "/api/classification/categories/<category_id>.<format>" using last identifier with values:
      | name        | My new category name |
      | slug        | my-new-category      |
      | description | My new description   |
      | enabled     | 1                    |
      | position    | 1                    |
    Then the response code should be 200
    And response should contain "<format>" object
    And response should contain "My new category name"
    And response should contain "my-new-category"
    And response should contain "My new description"

    When I send a GET request to "/api/classification/categories/<category_id>.<format>" using last identifier:
    Then the response code should be 200
    And response should contain "<format>" object
    And response should contain "My new category name"
    And response should contain "my-new-category"
    And response should contain "My new description"

    # DELETE

    When I send a DELETE request to "/api/classification/categories/<category_id>.<format>" using last identifier:
    Then the response code should be 200
    And response should contain "<format>" object
    And response should contain "true"

    When I send a GET request to "/api/classification/categories/<category_id>.<format>" using last identifier:
    Then the response code should be 404
    And response should contain "<format>" object

  Examples:
    | format  |
    | xml     |
    | json    |
