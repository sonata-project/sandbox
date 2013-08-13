@frontend
Feature: Check the blog frontend

Scenario: Check blog post list status code
  When I go to "blog/archive"
  Then the response status code should be 200

Scenario: Add a comment to a post
  When I go to "blog/archive"
  And I follow the first link of section "sonata-blog-post-title"
  And I fill in "comment_name" with "front test"
  And I fill in "comment_email" with "toto@local.host"
  And I fill in "comment_url" with "http://local.host"
  And I fill in "comment_message" with "This is my message"
  And I press "Add comment"
  Then I should see "This is my message"

