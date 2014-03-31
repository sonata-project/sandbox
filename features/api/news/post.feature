@api @news @post
Feature: Check the Post controller calls for NewsBundle

  # GET

  Scenario: Get all posts
    Given I am authenticating as "admin" with "admin" password
    When I send a GET request to "/api/news/posts.xml"
    Then the response code should be 200
    And response should contain "xml" object

 # POST

  Scenario: Post full workflow
    Given I am authenticating as "admin" with "admin" password
    When I send a POST request to "/api/news/posts.xml" with values:
      | title                 | My post title       |
      | slug                  | my-post-slug        |
      | abstract              | My abstract content |
      | rawContent            | My raw content      |
      | contentFormatter      | markdown            |
      | enabled               | 1                   |
      | commentsEnabled       | 1                   |
      | commentsDefaultStatus | 1                   |
      | author                | 1                   |
    Then the response code should be 200
    And response should contain "xml" object
    And response should contain "created_at"
    And store the XML response identifier as "identifier"

    Given I am authenticating as "admin" with "admin" password
    When I send a GET request to "/api/news/posts/<identifier>.xml" using last identifier:
    Then the response code should be 200
    And response should contain "xml" object
    And response should contain "My post title"
    And response should contain "my-post-slug"
    And response should contain "My abstract content"

    # PUT

    Given I am authenticating as "admin" with "admin" password
    When I send a PUT request to "/api/news/posts/<identifier>.xml" using last identifier with values:
      | title                 | My new post title       |
      | slug                  | my-new-post-slug        |
      | abstract              | My new abstract content |
      | rawContent            | My new raw content      |
      | contentFormatter      | markdown                |
      | enabled               | 1                       |
      | commentsEnabled       | 1                       |
      | commentsDefaultStatus | 1                       |
      | author                | 1                       |
    Then the response code should be 200
    And response should contain "xml" object
    And response should contain "My new post title"
    And response should contain "my-new-post-slug"
    And response should contain "My new abstract content"
    And response should contain "My new raw content"

    Given I am authenticating as "admin" with "admin" password
    When I send a GET request to "/api/news/posts/<identifier>.xml" using last identifier:
    Then the response code should be 200
    And response should contain "xml" object
    And response should contain "My new post title"
    And response should contain "my-new-post-slug"
    And response should contain "My new abstract content"
    And response should contain "My new raw content"

    # DELETE

    Given I am authenticating as "admin" with "admin" password
    When I send a DELETE request to "/api/news/posts/<identifier>.xml" using last identifier:
    Then the response code should be 200
    And response should contain "xml" object
    And response should contain "true"

    Given I am authenticating as "admin" with "admin" password
    When I send a GET request to "/api/news/posts/<identifier>.xml" using last identifier:
    Then the response code should be 404
    And response should contain "xml" object