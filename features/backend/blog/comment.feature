@backend
Feature: Check the comment admin module

Scenario: Check comment admin pages when not connected
  When I go to "admin/sonata/news/comment/list"
  Then the response status code should be 200
  And I should see "Username"

Scenario: Check comment admin pages when connected
  When I am connected with "admin" and "admin" on "admin/sonata/news/comment/list"
  Then I should see "Filters"