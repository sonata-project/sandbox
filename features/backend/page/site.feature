@backend
Feature: Check the site admin module

Scenario: Check site admin pages when not connected
  When I go to "admin/sonata/page/site/list"
  Then the response status code should be 200
  And I should see "Username"

Scenario: Check site admin pages when connected
  When I am connected with "admin" and "admin" on "admin/sonata/page/site/list"
  Then I should see "Filters"

Scenario: Add a new site with some errors
  When I am connected with "admin" and "admin" on "admin/sonata/page/site/create?uniqid=f155592a220e"
  And I press "Create"
  Then I should see "An error has occurred during the creation of item \"n/a\"."

Scenario: Add a new site
  When I am connected with "admin" and "admin" on "admin/sonata/page/site/create?uniqid=f155592a220e&siteId=1"
  And I fill in "f155592a220e_name" with "toto"
  And I fill in "f155592a220e_host" with "localhost"
  And I press "Create"
  Then I should see "Item \"toto\" has been successfully created."

Scenario: Filter sites
  When I am connected with "admin" and "admin" on "admin/sonata/page/site/list"
  And I fill in "filter_name_value" with "toto"
  And I press "Filter"
  Then I should see "toto"

Scenario: Edit a site
  When I am connected with "admin" and "admin" on "admin/sonata/page/site/list"
  And I fill in "filter_name_value" with "toto"
  And I press "Filter"
  And I follow "toto"
  And I press "Update"
  Then I should see "Item \"toto\" has been successfully updated."

Scenario: View revisions of a site
  When I am connected with "admin" and "admin" on "admin/sonata/page/site/list"
  And I fill in "filter_name_value" with "toto"
  And I press "Filter"
  And I follow "toto"
  And I follow "Revisions"
  Then the response status code should be 200

Scenario: Create site snapshots
  When I am connected with "admin" and "admin" on "admin/sonata/page/site/list"
  And I fill in "filter_name_value" with "toto"
  And I press "Filter"
  And I follow "Create snapshots"
  And I press "Create"
  Then I should see "Snapshots were successfully created."

Scenario: Show site
  When I am connected with "admin" and "admin" on "admin/sonata/page/site/list"
  And I fill in "filter_name_value" with "toto"
  And I press "Filter"
  And I follow "toto"
  And I follow "Show"
  Then the response status code should be 200

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

Scenario: Delete a site
  When I am connected with "admin" and "admin" on "admin/sonata/page/site/list"
  And I fill in "filter_name_value" with "toto"
  And I press "Filter"
  And I follow "toto"
  And I follow link "Delete" with class "btn btn-danger"
  And I press "Yes, delete"
  Then I should see "Item \"toto\" has been deleted successfully."
