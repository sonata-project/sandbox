@api @user
Feature: Check the User controller calls for UserBundle
  I want to test the different API calls

  Background:
    Given I am authenticating as "admin" with "admin" password

  # GET

  @api @user @list
  Scenario Outline: Retrieve all available users
    When I send a GET request to "<resource>"
    Then the response code should be <status_code>
    And response should contain "<format>" object
    And response should contain "<account_1>"
    And response should contain "<account_2>"

  Examples:
    | resource| status_code | format | account_1 | account_2 |
    | /api/user/users.json | 200 | json | admin | secure |
    | /api/user/users.xml  | 200 | xml  | admin | secure |

  # POST

  @api @user @new @ko
  Scenario: Post new user with errors
    When I send a POST request to "/api/user/users.xml" with values:
      | plainPassword | mypassword               |
      | email         | my@username.com          |
      | enabled       | 1                        |
      | expiresAt     | 2099-11-01T18:00:00+0100 |
      | firstname     | My firstname             |
      | lastname      | My lastname              |
    Then  the response code should be 500
    And response should contain "xml" object

  @api @user @workflow
  Scenario: User full workflow
    When I send a POST request to "/api/user/users.xml" with values:
      | username      | myusername               |
      | plainPassword | mypassword               |
      | email         | my@username.com          |
      | enabled       | 1                        |
      | expiresAt     | 2099-11-01T18:00:00+0100 |
      | firstname     | My firstname             |
      | lastname      | My lastname              |
    Then the response code should be 200
    And response should contain "xml" object
    And response should contain "created_at"
    And store the XML response identifier as "user_id"

    When I send a GET request to "/api/user/users/<user_id>.xml" using last identifier:
    Then the response code should be 200
    And response should contain "xml" object
    And response should contain "myusername"
    And response should contain "My firstname"
    And response should contain "My lastname"

    # PUT

    When I send a PUT request to "/api/user/users/<user_id>.xml" using last identifier with values:
      | username      | myusername               |
      | plainPassword | mypassword               |
      | email         | my@username.com          |
      | enabled       | 1                        |
      | expiresAt     | 2099-11-01T18:00:00+0100 |
      | firstname     | My new firstname         |
      | lastname      | My new lastname          |
    Then the response code should be 200
    And response should contain "xml" object
    And response should contain "My new firstname"
    And response should contain "My new lastname"

    When I send a GET request to "/api/user/users/<user_id>.xml" using last identifier:
    Then the response code should be 200
    And response should contain "xml" object
    And response should contain "My new firstname"
    And response should contain "My new lastname"

    # ATTACH GROUP

    When I send a POST request to "/api/user/groups.xml" with values:
      | name | My user custom group |
    Then the response code should be 200
    And response should contain "xml" object
    And response should contain "My user custom group"
    And store the XML response identifier as "group_id"

    When I send a POST request to "/api/user/users/<user_id>/groups/<group_id>.xml" using last identifier:
    Then the response code should be 200
    And response should contain "xml" object
    And response should contain "true"

    When I send a POST request to "/api/user/users/<user_id>/groups/<group_id>.xml" using last identifier:
    Then the response code should be 400
    And response should contain "xml" object
    And response should contain "already has group"

    # DELETE

    When I send a DELETE request to "/api/user/users/<user_id>.xml" using last identifier:
    Then the response code should be 200
    And response should contain "xml" object
    And response should contain "true"

    When I send a GET request to "/api/user/users/<user_id>.xml" using last identifier:
    Then the response code should be 404
    And response should contain "xml" object