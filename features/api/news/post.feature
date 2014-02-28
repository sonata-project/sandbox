@api @post @news
Feature: Check the POST API calls for NewsBundle

  Scenario: Post new comment (with errors)
    Given I am authenticating as "admin" with "admin" password
    When  I send a POST request to "/api/news/posts/1/comments.xml" with values:
      | comment[name]    | grou |
      | comment[email]   | grou |
      | comment[url]     | grou |
      | comment[message] | grou |
    Then  the response code should be 400
    And   the response should contain XML
    Then  response should contain "This value is not a valid email address."
    Then  response should contain "This value is not a valid URL."

  Scenario: Post new comment
    Given I am authenticating as "admin" with "admin" password
    When  I send a POST request to "/api/news/posts/1/comments.xml" with values:
      | comment[name]    | grou                      |
      | comment[email]   | em@il.com                 |
      | comment[url]     | http://sonata-project.org |
      | comment[message] | grou                      |
    Then  the response code should be 200
    And   the response should contain XML
    Then  response should contain "created_at"
