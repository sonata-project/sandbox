@api @user
Feature: Check the User controller calls for UserBundle
  I want to test the different API calls

  Background:
    Given I am authenticating as "admin" with "admin" password

  # GET

  @api @user @list
  Scenario Outline: Retrieve all available users
    When I send a GET request to "<resource>"
    Then the response code should be 200
    And response should contain "<format>" object
    And response pager should display page <page_number> with <per_page> elements
    And response pager data should be consistent

  Examples:
    | resource                                     | format | page_number | per_page |
    | /api/user/users.xml                          | xml    | 1           | 10       |
    | /api/user/users.xml?page=2&count=5           | xml    | 2           | 5        |
    | /api/user/users.json                         | json   | 1           | 10       |
    | /api/user/users.json?page=2&count=5          | json   | 2           | 5        |

  @api @user @group @unknown
  Scenario Outline: Get a specific user that not exists
    When I send a GET request to "/api/user/users/99999999999.<format>"
    Then the response code should be 404
    And response should contain "<format>" object
    And response should contain "User (99999999999) not found"

  Examples:
    | format  |
    | xml     |
    | json    |

  # POST

  @api @user @new @ko
  Scenario Outline: Post new user with errors
    When I send a POST request to "/api/user/users.<format>" with values:
      | plainPassword | mypassword               |
      | email         | my@username.com          |
      | enabled       | 1                        |
      | expiresAt     | 2099-11-01T18:00:00+0100 |
      | firstname     | My firstname             |
      | lastname      | My lastname              |
    Then  the response code should be 500
    And response should contain "<format>" object

  Examples:
    | format  |
    | xml     |
    | json    |

  @api @user @workflow
  Scenario Outline: User full workflow
    When I send a POST request to "/api/user/users.<format>" with values:
      | username      | myusername               |
      | plainPassword | mypassword               |
      | email         | my@username.com          |
      | enabled       | 1                        |
      | expiresAt     | 2099-11-01T18:00:00+0100 |
      | firstname     | My firstname             |
      | lastname      | My lastname              |
    Then the response code should be 200
    And response should contain "<format>" object
    And response should contain "created_at"
    And store the <format> response identifier as "user_id"

    When I send a GET request to "/api/user/users/<user_id>.<format>" using last identifier:
    Then the response code should be 200
    And response should contain "<format>" object
    And response should contain "myusername"
    And response should contain "My firstname"
    And response should contain "My lastname"

    # PUT

    When I send a PUT request to "/api/user/users/<user_id>.<format>" using last identifier with values:
      | username      | myusername               |
      | plainPassword | mypassword               |
      | email         | my@username.com          |
      | enabled       | 1                        |
      | expiresAt     | 2099-11-01T18:00:00+0100 |
      | firstname     | My new firstname         |
      | lastname      | My new lastname          |
    Then the response code should be 200
    And response should contain "<format>" object
    And response should contain "My new firstname"
    And response should contain "My new lastname"

    When I send a GET request to "/api/user/users/<user_id>.<format>" using last identifier:
    Then the response code should be 200
    And response should contain "<format>" object
    And response should contain "My new firstname"
    And response should contain "My new lastname"

    # ATTACH GROUP

    When I send a POST request to "/api/user/groups.<format>" with values:
      | name | My user custom group |
    Then the response code should be 200
    And response should contain "<format>" object
    And response should contain "My user custom group"
    And store the <format> response identifier as "group_id"

    When I send a POST request to "/api/user/users/<user_id>/groups/<group_id>.<format>" using last identifier:
    Then the response code should be 200
    And response should contain "<format>" object
    And response should contain "true"

    When I send a POST request to "/api/user/users/<user_id>/groups/<group_id>.<format>" using last identifier:
    Then the response code should be 400
    And response should contain "<format>" object
    And response should contain "already has group"

    # DETACH GROUP

    When I send a DELETE request to "/api/user/users/<user_id>/groups/<group_id>.<format>" using last identifier:
    Then the response code should be 200
    And response should contain "<format>" object
    And response should contain "true"

    When I send a DELETE request to "/api/user/users/<user_id>/groups/<group_id>.<format>" using last identifier:
    Then the response code should be 400
    And response should contain "<format>" object
    And response should contain "has not group"

    # DELETE GROUP

    When I send a DELETE request to "/api/user/groups/<group_id>.<format>" using last identifier:
    Then the response code should be 200
    And response should contain "<format>" object
    And response should contain "true"

    # DELETE

    When I send a DELETE request to "/api/user/users/<user_id>.<format>" using last identifier:
    Then the response code should be 200
    And response should contain "<format>" object
    And response should contain "true"

    When I send a GET request to "/api/user/users/<user_id>.<format>" using last identifier:
    Then the response code should be 404
    And response should contain "<format>" object

  Examples:
    | format  |
    | xml     |
    | json    |
