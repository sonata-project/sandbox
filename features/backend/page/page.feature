@backend
Feature: Check the page admin module

Scenario: Check page admin pages when not connected
  When I go to "admin/sonata/page/page/list"
  Then the response status code should be 200
  And I should see "Username"

Scenario: Check page admin pages when connected
  When I am connected with "admin" and "admin" on "admin/sonata/page/page/list"
  Then I should see "Filters"

Scenario: Add a new page with some errors
  When I am connected with "admin" and "admin" on "admin/sonata/page/page/create?uniqid=f155592a220e"
  And I follow "localhost"
  And I press "Create"
  Then I should see "An error has occurred during the creation of item \"-\"."

Scenario: Add a new page
  When I am connected with "admin" and "admin" on "admin/sonata/page/page/create?uniqid=f155592a220e"
  And I follow "localhost"
  And I fill in "f155592a220e_name" with "toto"
  And I fill in "f155592a220e_position" with "1"
  And I select "default" from "f155592a220e_templateCode"
  And I select "Home" from "f155592a220e_parent"
  And I press "Create"
  Then I should see "Item \"toto\" has been successfully created."

Scenario: Filter pages
  When I am connected with "admin" and "admin" on "admin/sonata/page/page/list"
  And I fill in "filter_name_value" with "toto"
  And I press "Filter"
  Then I should see "toto"

Scenario: Edit a page
  When I am connected with "admin" and "admin" on "admin/sonata/page/page/list"
  And I fill in "filter_name_value" with "toto"
  And I press "Filter"
  And I follow "toto"
  And I press "Update"
  Then I should see "Item \"toto\" has been successfully updated."

Scenario: View revisions of a page
  When I am connected with "admin" and "admin" on "admin/sonata/page/page/list"
  And I fill in "filter_name_value" with "toto"
  And I press "Filter"
  And I follow "toto"
  And I follow "Revisions"
  Then the response status code should be 200

Scenario: View content of a page
  When I am connected with "admin" and "admin" on "admin/sonata/page/page/list"
  And I fill in "filter_name_value" with "toto"
  And I press "Filter"
  And I follow "toto"
  And I follow "Show"
  Then the response status code should be 200

Scenario: Delete a page
  When I am connected with "admin" and "admin" on "admin/sonata/page/page/list"
  And I fill in "filter_name_value" with "toto"
  And I press "Filter"
  And I follow "toto"
  And I follow "Delete"
  And I press "Yes, delete"
  Then I should see "Item \"toto\" has been deleted successfully."

Scenario: Export JSON data
  When I am connected with "admin" and "admin" on "admin/sonata/page/page/list"
  And I follow "json"
  Then the response status code should be 200

Scenario: Export CSV data
  When I am connected with "admin" and "admin" on "admin/sonata/page/page/list"
  And I follow "csv"
  Then the response status code should be 200

Scenario: Export XML data
  When I am connected with "admin" and "admin" on "admin/sonata/page/page/list"
  And I follow "xml"
  Then the response status code should be 200

Scenario: Export XLS data
  When I am connected with "admin" and "admin" on "admin/sonata/page/page/list"
  And I follow "xls"
  Then the response status code should be 200
