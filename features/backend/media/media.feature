@backend
Feature: Check the media admin module

Scenario: Check media admin pages when not connected
  When I go to "admin/sonata/media/media/list"
  Then the response status code should be 200
  And I should see "Username"

Scenario: Check media admin pages when connected
  When I am connected with "admin" and "admin" on "admin/sonata/media/media/list"
  Then I should see "Filters"