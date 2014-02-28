@api @workflow @news
Feature: Check the POST API within a full workflow

  Scenario: Check if comment test does not already exist
    Given I am authenticating as "admin" with "admin" password
    When  I send a GET request to "/api/news/posts/1/comments.xml"
    Then  the response code should be 200
    Then  response should not contain "behat_comment_workflow_test"

  Scenario: Create comment test
    Given I am authenticating as "admin" with "admin" password
    When  I send a POST request to "/api/news/posts/1/comments.xml" with values:
      | comment[name]    | test                        |
      | comment[email]   | workflow@test.com           |
      | comment[url]     | http://sonata-project.org   |
      | comment[message] | behat_comment_workflow_test |
    Then  the response code should be 200
    And   the response should contain XML
    Then  response should contain "behat_comment_workflow_test"

  Scenario: Check if comment test has been saved
    Given I am authenticating as "admin" with "admin" password
    When  I send a GET request to "/api/news/posts/1/comments.xml"
    Then  the response code should be 200
    Then  response should contain "behat_comment_workflow_test"

