@backend
Feature: Check the user admin module

Scenario: Check user admin pages when not connected
  When I go to "admin/sonata/user/user/list"
  Then the response status code should be 200
  And I should see "Username"

Scenario: Check user admin pages when connected
  When I am connected with "admin" and "admin" on "admin/sonata/user/user/list"
  Then I should see "Filters"