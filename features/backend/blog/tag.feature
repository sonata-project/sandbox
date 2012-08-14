@backend
Feature: Check the tag admin module

Scenario: Check tag admin pages when not connected
  When I go to "admin/sonata/news/tag/list"
  Then the response status code should be 200
  And I should see "Username"

Scenario: Check category admin pages when connected
  When I am connected with "admin" and "admin" on "admin/sonata/news/tag/list"
  Then I should see "Filters"
  
Scenario: Add a new tag with some errors
  When I am connected with "admin" and "admin" on "admin/sonata/news/tag/create?uniqid=f155592a220e"
  And I press "Create"
  Then I should see "An error has occurred during item creation."

Scenario: Add a new tag
  When I am connected with "admin" and "admin" on "admin/sonata/news/tag/create?uniqid=f155592a220e"
  And I fill in "f155592a220e_name" with "toto"
  And I press "Create"
  Then I should see "Item has been successfully created."

Scenario: Filter tags
  When I am connected with "admin" and "admin" on "admin/sonata/news/tag/list"
  And I fill in "filter_name_value" with "toto"
  And I press "Filter"
  Then I should see "toto"

Scenario: Edit a tag
  When I am connected with "admin" and "admin" on "admin/sonata/news/tag/list"
  And I follow "toto"
  And I press "Update"
  Then I should see "Item has been successfully updated."

Scenario: View revisions of a tag
  When I am connected with "admin" and "admin" on "admin/sonata/news/tag/list"
  And I follow "toto"
  And I follow "Revisions"
  Then the response status code should be 200

Scenario: Export JSON data
  When I am connected with "admin" and "admin" on "admin/sonata/news/tag/list"
  And I follow "json"
  Then the response status code should be 200

Scenario: Export CSV data
  When I am connected with "admin" and "admin" on "admin/sonata/news/tag/list"
  And I follow "csv"
  Then the response status code should be 200

Scenario: Export XML data
  When I am connected with "admin" and "admin" on "admin/sonata/news/tag/list"
  And I follow "xml"
  Then the response status code should be 200

Scenario: Export XLS data
  When I am connected with "admin" and "admin" on "admin/sonata/news/tag/list"
  And I follow "xls"
  Then the response status code should be 200

Scenario: Delete a tag
  When I am connected with "admin" and "admin" on "admin/sonata/news/tag/list"
  And I fill in "filter_name_value" with "toto"
  And I press "Filter"
  And I follow "toto"
  And I follow "Delete"
  And I press "Yes, delete"
  Then I should see "Item has been deleted successfully."