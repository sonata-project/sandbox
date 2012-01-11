@backend
Feature: Check the category admin module

Scenario: Check category admin pages when not connected
  When I go to "admin/sonata/news/category/list"
  Then the response status code should be 200
  And I should see "Username"

Scenario: Check category admin pages when connected
  When I am connected with "admin" and "admin" on "admin/sonata/news/category/list"
  Then I should see "Filters"
