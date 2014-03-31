@api @user @group
Feature: Check the Group controller calls for UserBundle

  Background:
    Given I am authenticating as "admin" with "admin" password

  # GET

  Scenario: Get all groups
    When I send a GET request to "/api/user/groups.xml"
    Then the response code should be 200
    And response should contain "xml" object

  # POST

  Scenario: Post new group (with errors)
    When I send a POST request to "/api/user/groups.xml" with values:
      | name | |
    Then the response code should be 500
    And response should contain "xml" object

  Scenario: Group full workflow
    When I send a POST request to "/api/user/groups.xml" with values:
      | name | My custom group |
    Then the response code should be 200
    And response should contain "xml" object
    And response should contain "My custom group"
    And store the XML response identifier as "group_id"

    When I send a GET request to "/api/user/groups/<group_id>.xml" using last identifier:
    Then the response code should be 200
    And response should contain "xml" object
    And response should contain "My custom group"

    # PUT

    When I send a PUT request to "/api/user/groups/<group_id>.xml" using last identifier with values:
      | name | My new group name |
    Then the response code should be 200
    And response should contain "xml" object
    And response should contain "My new group name"

    When I send a GET request to "/api/user/groups/<group_id>.xml" using last identifier:
    Then the response code should be 200
    And response should contain "xml" object
    And response should contain "My new group name"

    # DELETE

    When I send a DELETE request to "/api/user/groups/<group_id>.xml" using last identifier:
    Then the response code should be 200
    And response should contain "xml" object
    And response should contain "true"

    When I send a GET request to "/api/user/groups/<group_id>.xml" using last identifier:
    Then the response code should be 404
    And response should contain "xml" object