@api @get @news
Feature: Check the GET API calls for NewsBundle

  Scenario: Check post list (json)
    Given I am authenticating as "admin" with "admin" password
    When  I send a GET request to "/api/news/posts.json"
    Then  the response code should be 200
    And   the response should contain json
    Then  response should contain "Now a specific gist from github"

  Scenario: Check post list (xml)
    Given I am authenticating as "admin" with "admin" password
    When  I send a GET request to "/api/news/posts.xml"
    Then  the response code should be 200
    And   the response should contain XML
    Then  response should contain "Now a specific gist from github"

  Scenario: Check unique post (json)
    Given I am authenticating as "admin" with "admin" password
    When  I send a GET request to "/api/news/posts/1.json"
    Then  the response code should be 200
    And   the response should contain json
    Then  response should contain "Now a specific gist from github"

  Scenario: Check unique post (xml)
    Given I am authenticating as "admin" with "admin" password
    When  I send a GET request to "/api/news/posts/1.xml"
    Then  the response code should be 200
    And   the response should contain XML
    Then  response should contain "Now a specific gist from github"

  Scenario: Check post comments (json)
    Given I am authenticating as "admin" with "admin" password
    When  I send a GET request to "/api/news/posts/1/comments.json"
    Then  the response code should be 200
    And   the response should contain json
    Then  response should contain "message"

  Scenario: Check unique post (xml)
    Given I am authenticating as "admin" with "admin" password
    When  I send a GET request to "/api/news/posts/1/comments.xml"
    Then  the response code should be 200
    And   the response should contain XML
    Then  response should contain "message"

  Scenario: Check post comments (json)
    Given I am authenticating as "admin" with "admin" password
    When  I send a GET request to "/api/news/comments/1.json"
    Then  the response code should be 200
    And   the response should contain json
    Then  response should contain "message"

  Scenario: Check post comments (xml)
    Given I am authenticating as "admin" with "admin" password
    When  I send a GET request to "/api/news/comments/1.xml"
    Then  the response code should be 200
    And   the response should contain XML
    Then  response should contain "message"
