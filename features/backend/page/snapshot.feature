@backend
Feature: Check the snapshot admin module

Scenario: Check snapshot admin pages when not connected
  When I go to "admin/sonata/page/snapshot/list"
  Then the response status code should be 200
  And I should see "Username"

Scenario: Check page admin pages when connected
  When I am connected with "admin" and "admin" on "admin/sonata/page/snapshot/list"
  Then I should see "Filters"

Scenario: Export JSON data
  When I am connected with "admin" and "admin" on "admin/sonata/page/site/list"
  And I follow "json"
  Then the response status code should be 200

Scenario: Export CSV data
  When I am connected with "admin" and "admin" on "admin/sonata/page/site/list"
  And I follow "csv"
  Then the response status code should be 200

Scenario: Export XML data
  When I am connected with "admin" and "admin" on "admin/sonata/page/site/list"
  And I follow "xml"
  Then the response status code should be 200

Scenario: Export XLS data
  When I am connected with "admin" and "admin" on "admin/sonata/page/site/list"
  And I follow "xls"
  Then the response status code should be 200
