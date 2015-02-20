@api @user @group
Feature: Check the Group controller calls for UserBundle
  I want to test the different API calls

  Background:
    Given I am authenticating as "admin" with "admin" password

  # GET

  @api @group @list
  Scenario Outline: Retrieve all available groups
    When I send a GET request to "<resource>"
    Then the response code should be 200
    And response should contain "<format>" object
    And response pager should display page <page_number> with <per_page> elements
    And response pager data should be consistent

  Examples:
    | resource                                      | format | page_number | per_page |
    | /api/user/groups.xml                          | xml    | 1           | 10       |
    | /api/user/groups.xml?page=1&count=5           | xml    | 1           | 5        |
    | /api/user/groups.json                         | json   | 1           | 10       |
    | /api/user/groups.json?page=1&count=5          | json   | 1           | 5        |

  @api @group @unknown
  Scenario Outline: Get a specific group that not exists
    When I send a GET request to "/api/user/groups/99999999999.<format>"
    Then the response code should be 404
    And response should contain "<format>" object
    And response should contain "Group (99999999999) not found"

  Examples:
    | format  |
    | xml     |
    | json    |

  # POST

  @api @group @new @ko
  Scenario Outline: Post new group with errors
    When I send a POST request to "/api/user/groups.<format>" with values:
      | name | |
    Then the response code should be 500
    And response should contain "<format>" object

  Examples:
    | format  |
    | xml     |
    | json    |

  @api @group @workflow
  Scenario Outline: Post a new group
    When I send a POST request to "/api/user/groups.<format>" with values:
      | name | My custom group |
    Then the response code should be 200
    And response should contain "<format>" object
    And response should contain "My custom group"
    And store the <format> response identifier as "group_id"

    When I send a GET request to "/api/user/groups/<group_id>.<format>" using last identifier:
    Then the response code should be 200
    And response should contain "<format>" object
    And response should contain "My custom group"

    # PUT

    When I send a PUT request to "/api/user/groups/<group_id>.<format>" using last identifier with values:
      | name | My new group name |
    Then the response code should be 200
    And response should contain "<format>" object
    And response should contain "My new group name"

    When I send a GET request to "/api/user/groups/<group_id>.<format>" using last identifier:
    Then the response code should be 200
    And response should contain "<format>" object
    And response should contain "My new group name"

    # DELETE

    When I send a DELETE request to "/api/user/groups/<group_id>.<format>" using last identifier:
    Then the response code should be 200
    And response should contain "<format>" object
    And response should contain "true"

    When I send a GET request to "/api/user/groups/<group_id>.<format>" using last identifier:
    Then the response code should be 404
    And response should contain "<format>" object

  Examples:
    | format  |
    | xml     |
    | json    |
