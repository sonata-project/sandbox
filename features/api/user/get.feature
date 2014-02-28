@api @get @user
Feature: Check the GET API calls for UserBundle

  Scenario: Check post list (json)
    Given I am authenticating as "admin" with "admin" password
    When  I send a GET request to "/api/user/users.json"
    Then  the response code should be 200
    And   the response should contain json
    Then  response should contain "gender"

  Scenario: Check post list (xml)
    Given I am authenticating as "admin" with "admin" password
    When  I send a GET request to "/api/user/users.xml"
    Then  the response code should be 200
    And   the response should contain XML
    Then  response should contain "gender"

  Scenario: Check unique post (json)
    Given I am authenticating as "admin" with "admin" password
    When  I send a GET request to "/api/user/users/1.json"
    Then  the response code should be 200
    And   the response should contain json
    Then  response should contain "gender"

  Scenario: Check unique post (xml)
    Given I am authenticating as "admin" with "admin" password
    When  I send a GET request to "/api/user/users/1.xml"
    Then  the response code should be 200
    And   the response should contain XML
    Then  response should contain "gender"

  Scenario: Check group list (json)
    Given I am authenticating as "admin" with "admin" password
    When  I send a GET request to "/api/user/groups.json"
    Then  the response code should be 200
    And   the response should contain json

  Scenario: Check group list (xml)
    Given I am authenticating as "admin" with "admin" password
    When  I send a GET request to "/api/user/groups.xml"
    Then  the response code should be 200
    And   the response should contain XML

  Scenario: Check unique group (json)
    Given I am authenticating as "admin" with "admin" password
    When  I send a GET request to "/api/user/groups/1.json"
    Then  the response code should be 404

  Scenario: Check unique group (xml)
    Given I am authenticating as "admin" with "admin" password
    When  I send a GET request to "/api/user/groups/1.xml"
    Then  the response code should be 404
