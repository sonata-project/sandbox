@backend
Feature: Check the page admin module

Scenario: Check page admin pages when not connected
  When I go to "admin/sonata/page/page/list"
  Then the response status code should be 200
  And I should see "Username"

Scenario: Check page admin pages when connected
  When I am connected with "admin" and "admin" on "admin/sonata/page/page/list"
  Then I should see "Filters"