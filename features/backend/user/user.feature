@backend
Feature: Check the user admin module

Scenario: Check user admin pages when not connected
  When I go to "admin/sonata/user/user/list"
  Then the response status code should be 200
  And I should see "Username"

Scenario: Check user admin pages when connected
  When I am connected with "admin" and "admin" on "admin/sonata/user/user/list"
  Then I should see "Filters"

Scenario: Add a new user with some errors
  When I am connected with "admin" and "admin" on "admin/sonata/user/user/create?uniqid=f155592a220e"
  And I press "Create"
  Then I should see "An error has occurred during the creation of item \"-\"."

Scenario: Add a new user
  When I am connected with "admin" and "admin" on "admin/sonata/user/user/create?uniqid=f155592a220e"
  And I fill in "f155592a220e_username" with "toto"
  And I fill in "f155592a220e_email" with "toto@local.host"
  And I fill in "f155592a220e_plainPassword" with "tata"
  And I press "Create"
  Then I should see "Item \"toto\" has been successfully created."

Scenario: Filter users
  When I am connected with "admin" and "admin" on "admin/sonata/user/user/list"
  And I fill in "filter_username_value" with "toto"
  And I press "Filter"
  Then I should see "toto"

Scenario: View revisions of a user
  When I am connected with "admin" and "admin" on "admin/sonata/user/user/list"
  And I fill in "filter_username_value" with "toto"
  And I press "Filter"
  And I follow "toto"
  And I follow "Revisions"
  Then the response status code should be 200

Scenario: Edit a user
  When I am connected with "admin" and "admin" on "admin/sonata/user/user/list"
  And I fill in "filter_username_value" with "toto"
  And I press "Filter"
  And I follow "toto"
  And I press "Update"
  Then I should see "Item \"toto\" has been successfully updated."

Scenario: Export JSON data
  When I am connected with "admin" and "admin" on "admin/sonata/user/user/list"
  And I follow "json"
  Then the response status code should be 200

Scenario: Export CSV data
  When I am connected with "admin" and "admin" on "admin/sonata/user/user/list"
  And I follow "csv"
  Then the response status code should be 200

Scenario: Export XML data
  When I am connected with "admin" and "admin" on "admin/sonata/user/user/list"
  And I follow "xml"
  Then the response status code should be 200

Scenario: Export XLS data
  When I am connected with "admin" and "admin" on "admin/sonata/user/user/list"
  And I follow "xls"
  Then the response status code should be 200

Scenario: Delete a user
  When I am connected with "admin" and "admin" on "admin/sonata/user/user/list"
  And I fill in "filter_username_value" with "toto"
  And I press "Filter"
  And I follow "toto"
  And I follow link "Delete" with class "btn btn-danger"
  And I press "Yes, delete"
  Then I should see "Item \"toto\" has been deleted successfully."
