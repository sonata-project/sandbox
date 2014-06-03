@api @news @comment
Feature: Check the Comment controller calls for NewsBundle

  Background:
    Given I am authenticating as "admin" with "admin" password

  # GET
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

  Scenario: Retrieves the comments of specified post which does not exists
    When I send a GET request to "/api/news/posts/999999999/comments.json"
    Then the response code should be 404
    And response should contain "Post (999999999) not found"

  Scenario Outline: Retrieves a specific comment
    When I send a GET request to "<resource>"
    Then the response code should be <status_code>
    And response should contain "<type>" object
    And response should contain "<author>"
    And response should contain "<message>"
  Examples:
    | resource                  | status_code | type | author                  | message                                 |
    | /api/news/comments/1.json | 200         | json | Ms. Sarina Hackett      | Velit aperiam culpa ut velit officia    |
    | /api/news/comments/2.xml  | 200         | xml  | Miss Sophie Murazik PhD | Beatae dignissimos possimus dignissimos |

  # POST
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
    | resource                             | status_code | type | name | email         | url                       | status | content                    | message    |
    | /api/news/posts/<post>/comments.xml  | 200         | xml  | Grou | grou@mail.com | http://sonata-project.org | 1      | Grou content               | created_at |
    | /api/news/posts/<post>/comments.json | 200         | json | Jess | jess@mail.com | http://www.jess.org       | 2      | Jess commented here!       | created_at |
    | /api/news/posts/<post>/comments.json | 200         | json | Jess |               |                           |        | Comment with less content! | created_at |

  Scenario: I can't add a comment on a missing post
    Given I have a Post identified by "post"
    When I send a POST request to "/api/news/posts/999999999/comments.xml" with values:
      | name    | Nath    |
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
      | name    | Nath    |
      | message | Nath commented a forbidden post |
    Then the response code should be 403
    And response should contain "xml" object
    And response should contain "not commentable"

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
    | resource                             | type | name | message         | email         | status | field   | error                                            |
    | /api/news/posts/<post>/comments.xml  | xml  |      | Unknown content | mail@mail.com | 1      | name    | Cette valeur ne doit pas être vide.              |
    | /api/news/posts/<post>/comments.json | json | Jess |                 | mail@mail.com | 1      | message | Cette valeur ne doit pas être vide.              |
    | /api/news/posts/<post>/comments.json | json | Jess | My content      | mail@mail.com | 99     | status  | Cette valeur doit être inférieure ou égale à 2.  |
    | /api/news/posts/<post>/comments.json | json | Jess | My content      | mail          | 1      | email   | Cette valeur n'est pas une adresse email valide. |

  # PUT
  Scenario Outline: Updates a comment
    Given I have a Post identified by "post"
    Given I have a Comment identified by "comment" on Post "post" with values:
      | name    | First author name    |
      | email   | new@email.org          |
      | url     | http://www.new-url.com |
      | message | My comment message |
      | status  | 1                      |
    When I send a PUT request to "<resource>" using identifier with values:
      | name    | <name>    |
      | email   | <mail>    |
      | url     | <url>     |
      | message | <message> |
      | status  | <status>  |
      | post    | <post_id> |
    Then the response code should be 200
    And response should contain "<type>" object
    And response should contain "<name>"
    And response should contain "<mail>"
    And response should contain "<message>"
    And response should contain "<status>"
  Examples:
    | resource                          | type | name | mail           | url                 | message       | status | post_id |
    | /api/news/comments/<comment>.json | json | Grou | grou@email.org | http://www.grou.com | Grou was here | 2      | <post>  |
    | /api/news/comments/<comment>.xml  | xml  | Jess | jess@email.org | http://www.jess.com | Jess was here | 1      | <post>  |

  Scenario Outline: I can't update a comment with invalid values
    Given I have a Post identified by "post"
    Given I have a Comment identified by "comment" on Post "post" with values:
      | name    | First author name    |
      | email   | new@email.org          |
      | url     | http://www.new-url.com |
      | message | My comment message |
      | status  | 1                      |
    When I send a PUT request to "<resource>" using identifier with values:
      | name    | <name>    |
      | email   | <mail>    |
      | url     | <url>     |
      | message | <message> |
      | status  | <status>  |
      | post    | <post_id> |
    Then the response code should be 400
    And response should contain "<type>" object
    And response should contain "Validation Failed"
    And the validation for "<field>" should fail with "<error>"
  Examples:
    | resource                          | type | name | mail          | url                | message         | status | post_id | field   | error                                            |
    | /api/news/comments/<comment>.xml  | xml  |      | mail@mail.com | http://www.url.com | Unknown content | 1      | <post>  | name    | Cette valeur ne doit pas être vide.              |
    | /api/news/comments/<comment>.json | json | Jess | mail@mail.com | http://www.url.com |                 | 1      | <post>  | message | Cette valeur ne doit pas être vide.              |
    | /api/news/comments/<comment>.json | json | Jess | mail@mail.com | http://www.url.com | My content      | 99     | <post>  | status  | Cette valeur doit être inférieure ou égale à 2.  |
    | /api/news/comments/<comment>.json | json | Jess | mail@mail.com | http://www.url.com | My content      | 1      | 999999  | post    | Cette valeur n'est pas valide.                   |
    | /api/news/comments/<comment>.json | json | Jess | mail          | http://www.url.com | My content      | 1      | <post>  | email   | Cette valeur n'est pas une adresse email valide. |


  # DELETE


#    When I send a POST request to "<resource>" using identifier with values:
#      | name    | <name>    |
#      | email   | <email>   |
#      | url     | <url>     |
#      | status  | <status>  |
#      | message | <content> |
#    Then the response code should be <status_code>
#    And response should contain "<type>" object
#    And response should contain "<message>"
#  Examples:
#    | resource                             | status_code | type | name | email         | url                       | status | content                    | message    |
#    | /api/news/posts/<post>/comments.xml  | 200         | xml  | Grou | grou@mail.com | http://sonata-project.org | 1      | Grou content               | created_at |
#    | /api/news/posts/<post>/comments.json | 200         | json | Jess | jess@mail.com | http://www.jess.org       | 2      | Jess commented here!       | created_at |
#    | /api/news/posts/<post>/comments.json | 200         | json | Jess |               |                           |        | Comment with less content! | created_at |


#  Scenario Outline: Adds a comment to a post with wrong parameters should trigger validation errors
#    When I send a POST request to "<resource>" using identifier with values:
#      | name    | <name>    |
#      | email   | <email>   |
#      | url     | <url>     |
#      | message | <content> |
#    Then the response code should be <status_code>
#    And response should contain "<type>" object
#    And response should contain "<message>"
#  Examples:
#    | resource                             | status_code | type | name | email         | url                       | content              | message    |
#    | /api/news/posts/<post>/comments.xml  | 200         | xml  | Grou | grou@mail.com | http://sonata-project.org | Grou content         | created_at |
#    | /api/news/posts/<post>/comments.json | 200         | json | Jess | jess@mail.com | http://www.jess.org       | Jess commented here! | created_at |

#  Scenario: Comment full workflow
#    When I send a POST request to "/api/news/posts.xml" with values:
#      | title                 | My post title       |
#      | slug                  | my-post-slug        |
#      | abstract              | My abstract content |
#      | rawContent            | My raw content      |
#      | contentFormatter      | markdown            |
#      | enabled               | 1                   |
#      | commentsEnabled       | 1                   |
#      | commentsDefaultStatus | 1                   |
#      | author                | 1                   |
#    Then the response code should be 200
#    And response should contain "xml" object
#    And response should contain "created_at"
#    And store the XML response identifier as "post_id"
#
#    # POST (comment)
#
#    When I send a POST request to "/api/news/posts/<post_id>/comments.xml" using last identifier with values:
#      | name    | grou                      |
#      | email   | em@il.com                 |
#      | url     | http://sonata-project.org |
#      | message | grou                      |
#    Then the response code should be 200
#    And response should contain "xml" object
#    And response should contain "created_at"
#    And store the XML response identifier as "comment_id"
#
#    When I send a GET request to "/api/news/comments/<comment_id>.xml" using last identifier:
#    Then the response code should be 200
#    And response should contain "xml" object
#    And response should contain "grou"
#    And response should contain "em@il.com"
#    And response should contain "sonata-project.org"
#
#    # PUT
#
#    When I send a PUT request to "/api/news/comments/<comment_id>.xml" using last identifier with values:
#      | name    | New comment name       |
#      | email   | new@email.org          |
#      | url     | http://www.new-url.com |
#      | message | My new comment message |
#      | status  | 1                      |
#      | post    | <post_id>              |
#    Then the response code should be 200
#    And response should contain "xml" object
#    And response should contain "New comment name"
#    And response should contain "new@email.org"
#    And response should contain "www.new-url.com"
#    And response should contain "My new comment message"
#
#    When I send a GET request to "/api/news/comments/<comment_id>.xml" using last identifier:
#    Then the response code should be 200
#    And response should contain "xml" object
#    And response should contain "New comment name"
#    And response should contain "new@email.org"
#    And response should contain "www.new-url.com"
#    And response should contain "My new comment message"
#
#    # DELETE
#
#    When I send a DELETE request to "/api/news/comments/<comment_id>.xml" using last identifier:
#    Then the response code should be 200
#    And response should contain "xml" object
#    And response should contain "true"
#
#    When I send a GET request to "/api/news/comments/<comment_id>.xml" using last identifier:
#    Then the response code should be 404
#    And response should contain "xml" object