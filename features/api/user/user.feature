@api @user
Feature: Check the User controller calls for UserBundle

  # GET

  Scenario: Get all users
    Given I am authenticating as "admin" with "admin" password
    When  I send a GET request to "/api/user/users.xml"
    Then  the response code should be 200
    And   the response should contain XML
    And   response should contain "admin"
    And   response should contain "secure"

  # POST

  Scenario: Post new user (with errors)
    Given I am authenticating as "admin" with "admin" password
    When  I send a POST request to "/api/user/users.xml" with values:
      | plainPassword | mypassword               |
      | email         | my@username.com          |
      | enabled       | 1                        |
      | expiresAt     | 2099-11-01T18:00:00+0100 |
      | firstname     | My firstname             |
      | lastname      | My lastname              |
    Then  the response code should be 500
    And   the response should contain XML

  Scenario: User full workflow
    Given I am authenticating as "admin" with "admin" password
    When  I send a POST request to "/api/user/users.xml" with values:
      | username      | myusername               |
      | plainPassword | mypassword               |
      | email         | my@username.com          |
      | enabled       | 1                        |
      | expiresAt     | 2099-11-01T18:00:00+0100 |
      | firstname     | My firstname             |
      | lastname      | My lastname              |
    Then  the response code should be 200
    And   the response should contain XML
    Then  response should contain "created_at"
    Then  store the XML response identifier as "user_id"

    Given I am authenticating as "admin" with "admin" password
    When  I send a GET request to "/api/user/users/<user_id>.xml" using last identifier:
    Then  the response code should be 200
    And   the response should contain XML
    And   response should contain "myusername"
    And   response should contain "My firstname"
    And   response should contain "My lastname"

    # PUT

    Given I am authenticating as "admin" with "admin" password
    When  I send a PUT request to "/api/user/users/<user_id>.xml" using last identifier with values:
      | username      | myusername               |
      | plainPassword | mypassword               |
      | email         | my@username.com          |
      | enabled       | 1                        |
      | expiresAt     | 2099-11-01T18:00:00+0100 |
      | firstname     | My new firstname         |
      | lastname      | My new lastname          |
    Then  the response code should be 200
    And   the response should contain XML
    And   response should contain "My new firstname"
    And   response should contain "My new lastname"

    Given I am authenticating as "admin" with "admin" password
    When  I send a GET request to "/api/user/users/<user_id>.xml" using last identifier:
    Then  the response code should be 200
    And   the response should contain XML
    And   response should contain "My new firstname"
    And   response should contain "My new lastname"

    # ATTACH GROUP

    Given I am authenticating as "admin" with "admin" password
    When  I send a POST request to "/api/user/groups.xml" with values:
      | name | My user custom group |
    Then  the response code should be 200
    And   the response should contain XML
    Then  response should contain "My user custom group"
    Then  store the XML response identifier as "group_id"

    Given I am authenticating as "admin" with "admin" password
    When  I send a POST request to "/api/user/users/<user_id>/groups/<group_id>.xml" using last identifier:
    Then  the response code should be 200
    And   the response should contain XML
    Then  response should contain "true"

    Given I am authenticating as "admin" with "admin" password
    When  I send a POST request to "/api/user/users/<user_id>/groups/<group_id>.xml" using last identifier:
    Then  the response code should be 400
    And   the response should contain XML
    Then  response should contain "already has group"

    # DELETE

    Given I am authenticating as "admin" with "admin" password
    When  I send a DELETE request to "/api/user/users/<user_id>.xml" using last identifier:
    Then  the response code should be 200
    And   the response should contain XML
    Then  response should contain "true"

    Given I am authenticating as "admin" with "admin" password
    When  I send a GET request to "/api/user/users/<user_id>.xml" using last identifier:
    Then  the response code should be 404
    And   the response should contain XML