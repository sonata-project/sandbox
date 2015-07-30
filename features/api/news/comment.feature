@api @news @comment
Feature: Check the Comment controller calls for NewsBundle

  Background:
    Given I am authenticating as "admin" with "admin" password

  # GET
  @ok @list
  Scenario Outline: Retrieves the comments of specified post
    When I send a GET request to "<resource>"
    Then the response code should be <status_code>
    And response should contain "<type>" object
    And response pager should display page <page_number> with <per_page> elements
    And response pager data should be consistent
  Examples:
    | resource                                        | status_code | type | page_number | per_page |
    | /api/news/posts/11/comments.json                | 200         | json | 1           | 10       |
    | /api/news/posts/11/comments.xml                 | 200         | xml  | 1           | 10       |
    | /api/news/posts/11/comments.json?page=2&count=1 | 200         | json | 2           | 1        |
    | /api/news/posts/11/comments.json?count=1        | 200         | json | 1           | 1        |

  @ko @list
  Scenario: Retrieves the comments of specified post which does not exists
    When I send a GET request to "/api/news/posts/999999999/comments.json"
    Then the response code should be 404
    And response should contain "Post (999999999) not found"

  Scenario Outline: Retrieves a specific comment
    Given I have a Post identified by "post"
    Given I have a Comment identified by "comment" on Post "post"
    When I send a GET request to "<resource>" using identifier
    Then the response code should be <status_code>
    And response should contain "<type>" object
    And response should contain "<author>"
    And response should contain "<message>"
  Examples:
    | resource                          | status_code | type | author           | message                |
    | /api/news/comments/<comment>.json | 200         | json | New comment name | My new comment message |
    | /api/news/comments/<comment>.xml  | 200         | xml  | New comment name | My new comment message |

  # POST
  @ok @new
  Scenario Outline: Adds a comment to a post
    Given I have a Post identified by "post"
    When I send a POST request to "<resource>" using identifier with values:
      | name    | <name>    |
      | email   | <email>   |
      | url     | <url>     |
      | status  | <status>  |
      | message | <content> |
    Then the response code should be <status_code>
    And response should contain "<type>" object
    And response should contain "<message>"
  Examples:
    | resource                             | status_code | type | name | email         | url                        | status | content                    | message    |
    | /api/news/posts/<post>/comments.xml  | 200         | xml  | Grou | grou@mail.com | https://sonata-project.org | 1      | Grou content               | created_at |
    | /api/news/posts/<post>/comments.json | 200         | json | Jess | jess@mail.com | http://www.jess.org        | 2      | Jess commented here!       | created_at |
    | /api/news/posts/<post>/comments.json | 200         | json | Jess |               |                            |        | Comment with less content! | created_at |

  @ko @new
  Scenario: I can't add a comment on a missing post
    Given I have a Post identified by "post"
    When I send a POST request to "/api/news/posts/999999999/comments.xml" with values:
      | name    | Nath                        |
      | message | Nath commented a ghost post |
    Then the response code should be 404
    And response should contain "xml" object
    And response should contain "Post (999999999) not found"

  Scenario: I can't add a comment on a post with commenting disabled
    Given I have a Post identified by "post" with values:
      | title                 | Post with disabled comments |
      | slug                  | post-without-comments       |
      | abstract              | Abstract content            |
      | rawContent            | Raw content                 |
      | contentFormatter      | markdown                    |
      | enabled               | 1                           |
      | commentsDefaultStatus | 1                           |
      | author                | 1                           |
      # commentsEnabled value is not specified, thus it is set to false
    When I send a POST request to "/api/news/posts/<post>/comments.xml" using identifier with values:
      | name    | Nath                            |
      | message | Nath commented a forbidden post |
    Then the response code should be 403
    And response should contain "xml" object

  @ko @new @validation
  Scenario Outline: I can't add a comment with invalid values
    Given I have a Post identified by "post"
    When I send a POST request to "<resource>" using identifier with values:
      | name    | <name>    |
      | message | <message> |
      | status  | <status>  |
      | email   | <email>   |
    Then the response code should be 400
    And response should contain "<type>" object
    And response should contain "Validation Failed"
    And the validation for "<field>" should fail with "<error>"
  Examples:
    | resource                             | type | name | message         | email         | status | field   | error                                    |
    | /api/news/posts/<post>/comments.xml  | xml  |      | Unknown content | mail@mail.com | 1      | name    | This value should not be blank.          |
    | /api/news/posts/<post>/comments.json | json | Jess |                 | mail@mail.com | 1      | message | This value should not be blank.          |
    | /api/news/posts/<post>/comments.json | json | Jess | My content      | mail@mail.com | 99     | status  | This value should be 2 or less.          |
    | /api/news/posts/<post>/comments.json | json | Jess | My content      | mail          | 1      | email   | This value is not a valid email address. |

  # PUT
  @ok @udpate
  Scenario Outline: Updates a comment
    Given I have a Post identified by "post"
    Given I have a Comment identified by "comment" on Post "post" with values:
      | name    | First author name      |
      | email   | new@email.org          |
      | url     | http://www.new-url.com |
      | message | My comment message     |
      | status  | 1                      |
    When I send a PUT request to "<resource>" using identifier with values:
      | name    | <name>    |
      | email   | <mail>    |
      | url     | <url>     |
      | message | <message> |
      | status  | <status>  |
    Then the response code should be 200
    And response should contain "<type>" object
    And response should contain "<name>"
    And response should contain "<mail>"
    And response should contain "<message>"
    And response should contain "<status>"
  Examples:
    | resource                                       | type | name | mail           | url                 | message       | status |
    | /api/news/posts/<post>/comments/<comment>.json | json | Grou | grou@email.org | http://www.grou.com | Grou was here | 2      |
    | /api/news/posts/<post>/comments/<comment>.xml  | xml  | Jess | jess@email.org | http://www.jess.com | Jess was here | 1      |

  @ko @update @validation
  Scenario Outline: I can't update a comment with invalid values
    Given I have a Post identified by "post"
    Given I have a Comment identified by "comment" on Post "post" with values:
      | name    | First author name      |
      | email   | new@email.org          |
      | url     | http://www.new-url.com |
      | message | My comment message     |
      | status  | 1                      |
    When I send a PUT request to "<resource>" using identifier with values:
      | name    | <name>    |
      | email   | <mail>    |
      | url     | <url>     |
      | message | <message> |
      | status  | <status>  |
    Then the response code should be 400
    And response should contain "<type>" object
    And response should contain "Validation Failed"
    And the validation for "<field>" should fail with "<error>"
  Examples:
    | resource                                       | type | name | mail          | url                | message         | status | field   | error                                    |
    | /api/news/posts/<post>/comments/<comment>.xml  | xml  |      | mail@mail.com | http://www.url.com | Unknown content | 1      | name    | This value should not be blank.          |
    | /api/news/posts/<post>/comments/<comment>.json | json | Jess | mail@mail.com | http://www.url.com |                 | 1      | message | This value should not be blank.          |
    | /api/news/posts/<post>/comments/<comment>.json | json | Jess | mail@mail.com | http://www.url.com | My content      | 99     | status  | This value should be 2 or less.          |
    | /api/news/posts/<post>/comments/<comment>.json | json | Jess | mail          | http://www.url.com | My content      | 1      | email   | This value is not a valid email address. |

  @ko @update
  Scenario Outline: I can't update a comment that does not exists
    Given I have a Post identified by "post"
    When I send a PUT request to "<resource>" using identifier with values:
      | name    | Jess               |
      | email   | mail@mail.com      |
      | url     | http://www.url.com |
      | message | My content         |
      | status  | 1                  |
    Then the response code should be 404
    And response should contain "Comment (999999999) not found"
  Examples:
    | resource                                       |
    | /api/news/posts/<post>/comments/999999999.json |

  # DELETE
  @ok @delete
  Scenario Outline: Deletes a comment
    Given I have a Post identified by "post"
    Given I have a Comment identified by "comment" on Post "post"
    When I send a DELETE request to "<resource>" using identifier
    Then the response code should be <status_code>
    And response should contain "<type>" object
    And response should contain "<message>"
  Examples:
    | resource                          | status_code | type | message                 |
    | /api/news/comments/<comment>.xml  | 200         | xml  | true                    |
    | /api/news/comments/<comment>.json | 200         | json | true                    |
    | /api/news/comments/404.json       | 404         | json | Comment (404) not found |