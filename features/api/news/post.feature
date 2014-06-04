@api @news @post
Feature: Check the Post controller calls for NewsBundle
  In order to manage posts
  As the admin user
  I want to be able to add, update or delete posts via the api

  Background:
    Given I am authenticating as "admin" with "admin" password

  # GET
  @ok @list
  Scenario Outline: Retrieves the list of posts (paginated) based on criteria
    When I send a GET request to "<resource>"
    Then the response code should be <status_code>
    And response should contain "<type>" object
    And response pager should display page <page_number> with <per_page> elements
    And response pager data should be consistent
    And response should contain "<tag>"
  Examples:
    | resource                                     | status_code | type | page_number | per_page | tag     |
    | /api/news/posts.xml                          | 200         | xml  | 1           | 10       |         |
    | /api/news/posts.xml?tag=web2                 | 200         | xml  | 1           | 10       | web2    |
    | /api/news/posts.json?page=2&count=5&tag=web2 | 200         | json | 2           | 5        | web2    |

  @ok @id
  Scenario Outline: Retrieves a specific post
    Given I have a Post identified by "post"
    When I send a GET request to "<resource>" using identifier
    Then the response code should be <status_code>
    And response should contain "<type>" object
    And response should contain "<title>"
    And response should contain "<slug>"
    And response should contain "<content>"
  Examples:
    | resource                    | status_code | type | title         | slug         | content              |
    | /api/news/posts/<post>.json | 200         | json | My post title | my-post-slug | My abstract content  |
    | /api/news/posts/<post>.xml  | 200         | xml  | My post title | my-post-slug | My abstract content  |
    | /api/news/posts/404.json    | 404         | json |               |              | Post (404) not found |

  # POST
  @ok @new
  Scenario Outline: Adds a post
    When I send a POST request to "<resource>" with values:
      | title                 | <title>       |
      | slug                  | <slug>        |
      | abstract              | <content>     |
      | rawContent            | <raw_content> |
      | contentFormatter      | markdown      |
      | enabled               | 1             |
      | commentsEnabled       | 1             |
      | commentsDefaultStatus | 1             |
      | author                | 1             |
    Then the response code should be <status_code>
    And response should contain "<type>" object
    And response should contain "<title>"
    And response should contain "<slug>"
    And response should contain "<message>"
    And response should contain "publication_date_start"
  Examples:
    | resource             | status_code | type | title                | slug                | content                    | raw_content           | message           |
    | /api/news/posts.xml  | 200         | xml  | My post title        | my-post-slug        | My abstract content        | My raw content        | created_at        |
    | /api/news/posts.json | 200         | json | My second post title | my-second-post-slug | My second abstract content | My second raw content | created_at        |

  @ko @new @validation
  Scenario Outline: I can't add a post with invalid values
    When I send a POST request to "<resource>" with values:
      | title                 | <title>                   |
      | slug                  | <slug>                    |
      | abstract              | <content>                 |
      | rawContent            | <raw_content>             |
      | contentFormatter      | <content_formatter>       |
      | enabled               | <enabled>                 |
      | commentsEnabled       | <comments_enabled>        |
      | commentsDefaultStatus | <comments_default_status> |
    Then the response code should be 400
    And response should contain "<type>" object
    And response should contain "Validation Failed"
    And the validation for "<field>" should fail with "<error>"
  Examples:
    | resource             | type | title    | slug         | content             | raw_content    | content_formatter | enabled | comments_enabled | comments_default_status | field                 | error                           |
    | /api/news/posts.json | json |          | my-post-slug | My abstract content | My raw content | markdown          | 1       | 1                | 1                       | title                 | This value should not be blank. |
    | /api/news/posts.xml  | xml  |          | my-post-slug | My abstract content | My raw content | markdown          | 1       | 1                | 1                       | title                 | This value should not be blank. |
    | /api/news/posts.xml  | xml  | My title | my-post-slug |                     | My raw content | markdown          | 1       | 1                | 1                       | abstract              | This value should not be blank. |
    | /api/news/posts.xml  | xml  | My title | my-post-slug | My abstract content |                | markdown          | 1       | 1                | 1                       | rawContent            | This value should not be blank. |
    | /api/news/posts.xml  | xml  |          | my-post-slug | My abstract content | My raw content | markdown          | 1       | 1                | 1                       | title                 | This value should not be blank. |
    | /api/news/posts.xml  | xml  | My title | my-post-slug | My abstract content | My raw content | markdown          | 1       | 1                |                         | commentsDefaultStatus | This value should not be blank. |
    | /api/news/posts.xml  | xml  | My title | my-post-slug | My abstract content | My raw content |                   | 1       | 1                | 1                       | contentFormatter      | The formatter is not valid      |
    | /api/news/posts.xml  | xml  | My title |              | My abstract content | My raw content | markdown          | 1       | 1                | 1                       | slug                  | This value should not be blank. |

  # PUT
  @ok @update
  Scenario Outline: Updates a post
    Given I have a Post identified by "post" with values:
      | title                 | Cats & dogs        |
      | slug                  | cats-and-dogs      |
      | abstract              | Cats hate dogs     |
      | rawContent            | Cats raw hate dogs |
      | contentFormatter      | markdown           |
      | enabled               | 1                  |
      | commentsEnabled       | 1                  |
      | commentsDefaultStatus | 1                  |
      | author                | 1                  |
    When I send a PUT request to "<resource>" using identifier with values:
      | title                 | <title>       |
      | slug                  | <slug>        |
      | abstract              | <content>     |
      | rawContent            | <raw_content> |
      | contentFormatter      | markdown      |
      | enabled               | 1             |
      | commentsEnabled       | 1             |
      | commentsDefaultStatus | 1             |
      | author                | 1             |
    Then the response code should be <status_code>
    And response should contain "<type>" object
    And response should contain "<title>"
    And response should contain "<slug>"
    And response should contain "<content>"
    And response should contain "<raw_content>"
    And response should contain "publication_date_start"
  Examples:
    | resource                    | status_code | type | title           | slug            | content         | raw_content         |
    | /api/news/posts/<post>.xml  | 200         | xml  | Cats love dogs  | cats-love-dogs  | Cats love dogs  | Cats raw love dogs  |
    | /api/news/posts/<post>.json | 200         | json | Cats and fishes | cats-and-fishes | Cats eat fishes | Cats raw eat fishes |

  @ko @update @validation
  Scenario Outline: I can't update a post with invalid values
    Given I have a Post identified by "post"
    When I send a PUT request to "<resource>" using identifier with values:
      | title                 | <title>                   |
      | slug                  | <slug>                    |
      | abstract              | <content>                 |
      | rawContent            | <raw_content>             |
      | contentFormatter      | <content_formatter>       |
      | enabled               | <enabled>                 |
      | commentsEnabled       | <comments_enabled>        |
      | commentsDefaultStatus | <comments_default_status> |
    Then the response code should be 400
    And response should contain "<type>" object
    And response should contain "Validation Failed"
    And the validation for "<field>" should fail with "<error>"
  Examples:
    | resource                    | type | title    | slug         | content             | raw_content    | content_formatter | enabled | comments_enabled | comments_default_status | field                 | error                               |
    | /api/news/posts/<post>.json | json |          | my-post-slug | My abstract content | My raw content | markdown          | 1       | 1                | 1                       | title                 | This value should not be blank. |
    | /api/news/posts/<post>.xml  | xml  |          | my-post-slug | My abstract content | My raw content | markdown          | 1       | 1                | 1                       | title                 | This value should not be blank. |
    | /api/news/posts/<post>.xml  | xml  | My title | my-post-slug |                     | My raw content | markdown          | 1       | 1                | 1                       | abstract              | This value should not be blank. |
    | /api/news/posts/<post>.xml  | xml  | My title | my-post-slug | My abstract content |                | markdown          | 1       | 1                | 1                       | rawContent            | This value should not be blank. |
    | /api/news/posts/<post>.xml  | xml  |          | my-post-slug | My abstract content | My raw content | markdown          | 1       | 1                | 1                       | title                 | This value should not be blank. |
    | /api/news/posts/<post>.xml  | xml  | My title | my-post-slug | My abstract content | My raw content | markdown          | 1       | 1                |                         | commentsDefaultStatus | This value should not be blank. |
    | /api/news/posts/<post>.xml  | xml  | My title | my-post-slug | My abstract content | My raw content |                   | 1       | 1                | 1                       | contentFormatter      | The formatter is not valid          |
    | /api/news/posts/<post>.xml  | xml  | My title |              | My abstract content | My raw content | markdown          | 1       | 1                | 1                       | slug                  | This value should not be blank. |

  @ko @update
  Scenario: Updates a post which does not exist returns not found
    When I send a PUT request to "/api/news/posts/999999999.json" with values:
      | title                 | Cats & dogs        |
      | slug                  | cats-and-dogs      |
      | abstract              | Cats hate dogs     |
      | rawContent            | Cats raw hate dogs |
      | contentFormatter      | markdown           |
      | enabled               | 1                  |
      | commentsEnabled       | 1                  |
      | commentsDefaultStatus | 1                  |
      | author                | 1                  |
    Then the response code should be 404
    And response should contain "Post (999999999) not found"

  # DELETE
  @ok @delete
  Scenario Outline: Deletes a post
    Given I have a Post identified by "post"
    When I send a DELETE request to "<resource>" using identifier
    Then the response code should be <status_code>
    And response should contain "<type>" object
    And response should contain "<message>"
  Examples:
    | resource                    | status_code | type | message               |
    | /api/news/posts/<post>.xml  | 200         | xml  | true                  |
    | /api/news/posts/<post>.json | 200         | json | true                  |
    | /api/news/posts/404.json    | 404         | json | Post (404) not found  |