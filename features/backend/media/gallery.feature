@backend
Feature: Check the gallery admin module

Scenario: Check gallery admin pages when not connected
  When I go to "admin/sonata/media/gallery/list"
  Then the response status code should be 200
  And I should see "Username"

Scenario: Check gallery admin pages when connected
  When I am connected with "admin" and "admin" on "admin/sonata/media/gallery/list"
  Then I should see "Filters"