@api @classification @collection
Feature: Check the API for ClassificationBundle
  I want to test the API calls about collection


  Background:
    Given I am authenticating as "admin" with "admin" password

  # GET

  @api @classification @collection @list
  Scenario Outline: Get all collections
    When I send a GET request to "<resource>"
    Then the response code should be 200
    And response should contain "<format>" object
    And response pager should display page <page_number> with <per_page> elements
    And response pager data should be consistent

  Examples:
    | resource                                                      | format | page_number | per_page |
    | /api/classification/collections.xml                           | xml    | 1           | 10       |
    | /api/classification/collections.xml?page=1&count=5            | xml    | 1           | 5        |
    | /api/classification/collections.json                          | json   | 1           | 10       |
    | /api/classification/collections.json?page=1&count=5           | json   | 1           | 5        |

  @api @classification @collection @unknown
  Scenario Outline: Get a specific collection that not exists
    When I send a GET request to "/api/classification/collections/99999999999.<format>"
    Then the response code should be 404
    And response should contain "<format>" object
    And response should contain "Collection (99999999999) not found"

  Examples:
    | format  |
    | xml     |
    | json    |

  # POST

  @api @classification @collection @new @ko
  Scenario Outline: Post new collection (with errors)
    When I send a POST request to "/api/classification/collections.<format>" with values:
      | description | My description |
      | enabled     | 1              |
    Then the response code should be 400
    And response should contain "<format>" object
    And response should contain "Validation Failed"
    And response should contain "This value should not be blank"

  Examples:
    | format  |
    | xml     |
    | json    |

  @api @classification @collection @workflow
  Scenario Outline: Collection full workflow
    When I send a POST request to "/api/classification/collections.<format>" with values:
      | name        | My collection  |
      | slug        | my-collection  |
      | description | My description |
      | enabled     | 1              |
    Then the response code should be 200
    And response should contain "<format>" object
    And response should contain "created_at"
    And store the <format> response identifier as "collection_id"

    When I send a GET request to "/api/classification/collections/<collection_id>.<format>" using last identifier:
    Then the response code should be 200
    And response should contain "<format>" object
    And response should contain "My collection"
    And response should contain "my-collection"
    And response should contain "My description"

    # PUT

    When  I send a PUT request to "/api/classification/collections/<collection_id>.<format>" using last identifier with values:
      | name        | My new collection name |
      | slug        | my-new-collection      |
      | description | My new description     |
      | enabled     | 1                      |
    Then the response code should be 200
    And response should contain "<format>" object
    And response should contain "My new collection name"
    And response should contain "my-new-collection"
    And response should contain "My new description"

    When I send a GET request to "/api/classification/collections/<collection_id>.<format>" using last identifier:
    Then the response code should be 200
    And response should contain "<format>" object
    And response should contain "My new collection name"
    And response should contain "my-new-collection"
    And response should contain "My new description"

    # DELETE

    When I send a DELETE request to "/api/classification/collections/<collection_id>.<format>" using last identifier:
    Then the response code should be 200
    And response should contain "<format>" object
    And response should contain "true"

    When I send a GET request to "/api/classification/collections/<collection_id>.<format>" using last identifier:
    Then the response code should be 404
    And response should contain "<format>" object

  Examples:
    | format  |
    | xml     |
    | json    |
