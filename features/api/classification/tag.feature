@api @classification @tag
Feature: Check the API for ClassificationBundle
  I want to test the API calls about tag

  Background:
    Given I am authenticating as "admin" with "admin" password

  # GET

  @api @classification @tag @list
  Scenario Outline: Get all tags
    When I send a GET request to "<resource>"
    Then the response code should be 200
    And response should contain "<format>" object
    And response pager should display page <page_number> with <per_page> elements
    And response pager data should be consistent

  Examples:
    | resource                                              | format | page_number | per_page |
    | /api/classification/tags.xml                          | xml    | 1           | 10       |
    | /api/classification/tags.xml?page=1&count=5           | xml    | 1           | 5        |
    | /api/classification/tags.json                         | json   | 1           | 10       |
    | /api/classification/tags.json?page=1&count=5          | json   | 1           | 5        |

  @api @classification @tag @unknown
  Scenario Outline: Get a specific tag that not exists
    When I send a GET request to "/api/classification/tags/99999999999.<format>"
    Then the response code should be 404
    And response should contain "<format>" object
    And response should contain "Tag (99999999999) not found"

  Examples:
    | format  |
    | xml     |
    | json    |


  # POST

  @api @classification @tag @new @ko
  Scenario Outline: Post new tag (with errors)
    When I send a POST request to "/api/classification/tags.<format>" with values:
      | enabled     | 1         |
    Then the response code should be 400
    And response should contain "<format>" object
    And response should contain "Validation Failed"
    And response should contain "This value should not be blank"

  Examples:
    | format  |
    | xml     |
    | json    |

  @api @classification @tag @workflow
  Scenario Outline: Tag full workflow
    When I send a POST request to "/api/classification/tags.<format>" with values:
      | name        | My tag    |
      | slug        | my-tag    |
      | enabled     | 1         |
    Then  the response code should be 200
    And response should contain "<format>" object
    And response should contain "created_at"
    And store the <format> response identifier as "tag_id"

    When I send a GET request to "/api/classification/tags/<tag_id>.<format>" using last identifier:
    Then the response code should be 200
    And response should contain "<format>" object
    And response should contain "My tag"
    And response should contain "my-tag"

    # PUT

    When I send a PUT request to "/api/classification/tags/<tag_id>.<format>" using last identifier with values:
      | name        | My new tag name |
      | slug        | my-new-tag      |
      | enabled     | 1               |
    Then the response code should be 200
    And response should contain "<format>" object
    And response should contain "My new tag name"
    And response should contain "my-new-tag"

    When I send a GET request to "/api/classification/tags/<tag_id>.<format>" using last identifier:
    Then the response code should be 200
    And response should contain "<format>" object
    And response should contain "My new tag name"
    And response should contain "my-new-tag"

    # DELETE

    When I send a DELETE request to "/api/classification/tags/<tag_id>.<format>" using last identifier:
    Then the response code should be 200
    And response should contain "<format>" object
    And response should contain "true"

    When I send a GET request to "/api/classification/tags/<tag_id>.<format>" using last identifier:
    Then the response code should be 404
    And response should contain "<format>" object

  Examples:
    | format  |
    | xml     |
    | json    |
