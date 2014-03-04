@api @news @comment
Feature: Check the Comment controller calls for NewsBundle

 # POST

  Scenario: Comment full workflow
    Given I am authenticating as "admin" with "admin" password
    When  I send a POST request to "/api/news/posts.xml" with values:
      | title                 | My post title       |
      | slug                  | my-post-slug        |
      | abstract              | My abstract content |
      | rawContent            | My raw content      |
      | contentFormatter      | markdown            |
      | enabled               | 1                   |
      | commentsEnabled       | 1                   |
      | commentsDefaultStatus | 1                   |
      | author                | 1                   |
    Then  the response code should be 200
    And   the response should contain XML
    And   response should contain "created_at"
    Then  store the XML response identifier as "post_id"

    # POST (comment)

    Given I am authenticating as "admin" with "admin" password
    When  I send a POST request to "/api/news/posts/<post_id>/comments.xml" using last identifier with values:
      | name    | grou                      |
      | email   | em@il.com                 |
      | url     | http://sonata-project.org |
      | message | grou                      |
    Then  the response code should be 200
    And   the response should contain XML
    Then  response should contain "created_at"
    Then  store the XML response identifier as "comment_id"

    Given I am authenticating as "admin" with "admin" password
    When  I send a GET request to "/api/news/comments/<comment_id>.xml" using last identifier:
    Then  the response code should be 200
    And   the response should contain XML
    And   response should contain "grou"
    And   response should contain "em@il.com"
    And   response should contain "sonata-project.org"

    # PUT

    Given I am authenticating as "admin" with "admin" password
    When  I send a PUT request to "/api/news/comments/<comment_id>.xml" using last identifier with values:
      | name    | New comment name       |
      | email   | new@email.org          |
      | url     | http://www.new-url.com |
      | message | My new comment message |
      | status  | 1                      |
      | post    | <post_id>              |
    Then  the response code should be 200
    And   the response should contain XML
    Then  response should contain "New comment name"
    And   response should contain "new@email.org"
    And   response should contain "www.new-url.com"
    And   response should contain "My new comment message"

    Given I am authenticating as "admin" with "admin" password
    When  I send a GET request to "/api/news/comments/<comment_id>.xml" using last identifier:
    Then  the response code should be 200
    And   the response should contain XML
    Then  response should contain "New comment name"
    And   response should contain "new@email.org"
    And   response should contain "www.new-url.com"
    And   response should contain "My new comment message"

    # DELETE

    Given I am authenticating as "admin" with "admin" password
    When  I send a DELETE request to "/api/news/comments/<comment_id>.xml" using last identifier:
    Then  the response code should be 200
    And   the response should contain XML
    Then  response should contain "true"

    Given I am authenticating as "admin" with "admin" password
    When  I send a GET request to "/api/news/comments/<comment_id>.xml" using last identifier:
    Then  the response code should be 404
    And   the response should contain XML