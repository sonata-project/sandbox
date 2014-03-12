@api @user @group
Feature: Check the Group controller calls for UserBundle

  # GET

  Scenario: Get all groups
    Given I am authenticating as "admin" with "admin" password
    When  I send a GET request to "/api/user/groups.xml"
    Then  the response code should be 200
    And   the response should contain XML

  # POST

  Scenario: Post new group (with errors)
    Given I am authenticating as "admin" with "admin" password
    When  I send a POST request to "/api/user/groups.xml" with values:
      | name | |
    Then  the response code should be 500
    And   the response should contain XML

  Scenario: Group full workflow
    Given I am authenticating as "admin" with "admin" password
    When  I send a POST request to "/api/user/groups.xml" with values:
      | name | My custom group |
    Then  the response code should be 200
    And   the response should contain XML
    Then  response should contain "My custom group"
    Then  store the XML response identifier as "group_id"

    Given I am authenticating as "admin" with "admin" password
    When  I send a GET request to "/api/user/groups/<group_id>.xml" using last identifier:
    Then  the response code should be 200
    And   the response should contain XML
    And   response should contain "My custom group"

    # PUT

    Given I am authenticating as "admin" with "admin" password
    When  I send a PUT request to "/api/user/groups/<group_id>.xml" using last identifier with values:
      | name | My new group name |
    Then  the response code should be 200
    And   the response should contain XML
    And   response should contain "My new group name"

    Given I am authenticating as "admin" with "admin" password
    When  I send a GET request to "/api/user/groups/<group_id>.xml" using last identifier:
    Then  the response code should be 200
    And   the response should contain XML
    And   response should contain "My new group name"

    # DELETE

    Given I am authenticating as "admin" with "admin" password
    When  I send a DELETE request to "/api/user/groups/<group_id>.xml" using last identifier:
    Then  the response code should be 200
    And   the response should contain XML
    Then  response should contain "true"

    Given I am authenticating as "admin" with "admin" password
    When  I send a GET request to "/api/user/groups/<group_id>.xml" using last identifier:
    Then  the response code should be 404
    And   the response should contain XML