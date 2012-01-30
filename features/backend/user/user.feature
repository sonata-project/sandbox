@backend
Feature: Check the user admin module

Scenario: Check user admin pages when not connected
  When I go to "admin/sonata/user/user/list"
  Then the response status code should be 200
  And I should see "Username"

Scenario: Check user admin pages when connected
  When I am connected with "admin" and "admin" on "admin/sonata/user/user/list"
  Then I should see "Filters"

Scenario: Add a new User with some errors
  When I am connected with "admin" and "admin" on "admin/sonata/user/user/create?uniqid=4f155592a220e"
  And I press "Create"
  Then I should see "An error has occurred during item creation."

Scenario: Add a new User with a bad email
  When I am connected with "admin" and "admin" on "admin/sonata/user/user/create?uniqid=4f155592a220e"
  And I fill in "4f155592a220e_username" with "toto"
  And I fill in "4f155592a220e_email" with "toto"
  And I press "Create"
  Then I should see "An error has occurred during item creation."

Scenario: Add a new User
  When I am connected with "admin" and "admin" on "admin/sonata/user/user/create?uniqid=4f155592a220e"
  And I fill in "4f155592a220e_username" with "toto"
  And I fill in "4f155592a220e_email" with "toto@ekino.com"
  And I press "Create"
  Then I should see "Item has been successfully created."

Scenario: Edit a User
  When I am connected with "admin" and "admin" on "admin/sonata/user/user/list"
  And I follow "toto"
  And I press "Update"
  Then I should see "Item has been successfully updated."

Scenario: Delete a user
  When I am connected with "admin" and "admin" on "admin/sonata/user/user/list"
  And I fill in "filter_name_value" with "toto"
  And I press "Filter"
  And I follow "toto"
  And I follow "Delete"
  And I press "Yes, delete"
  Then I should see "Item has been deleted successfully."