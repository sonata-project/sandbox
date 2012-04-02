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
  When I am connected with "admin" and "admin" on "admin/sonata/page/page/create?uniqid=4f155592a220e&siteId=1"
  And I press "Create"
  Then I should see "An error has occurred during item creation."

Scenario: Add a new page
  When I am connected with "admin" and "admin" on "admin/sonata/page/page/create?uniqid=4f155592a220e&siteId=1"
  And I fill in "4f155592a220e_name" with "toto"
  And I fill in "4f155592a220e_position" with "1"
  And I select "default" from "4f155592a220e_templateCode"
  And I press "Create"
  Then I should see "Item has been successfully created."

Scenario: Edit a page
  When I am connected with "admin" and "admin" on "admin/sonata/page/page/list"
  And I fill in "filter_name_value" with "toto"
  And I press "Filter"
  And I follow "toto"
  And I press "Update"
  Then I should see "Item has been successfully updated."

Scenario: Delete a page
  When I am connected with "admin" and "admin" on "admin/sonata/page/page/list"
  And I fill in "filter_name_value" with "toto"
  And I press "Filter"
  And I follow "toto"
  And I follow "Delete"
  And I press "Yes, delete"
  Then I should see "Item has been deleted successfully."