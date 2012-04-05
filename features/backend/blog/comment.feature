@backend
Feature: Check the comment admin module

Scenario: Check comment admin pages when not connected
  When I go to "admin/sonata/news/comment/list"
  Then the response status code should be 200
  And I should see "Username"

Scenario: Check comment admin pages when connected
  When I am connected with "admin" and "admin" on "admin/sonata/news/comment/list"
  Then I should see "Filters"

Scenario: Add a new comment with some errors
  When I am connected with "admin" and "admin" on "admin/sonata/news/comment/create?uniqid=4f155592a220e"
  And I press "Create"
  Then I should see "An error has occurred during item creation."

Scenario: Add a new comment
  When I am connected with "admin" and "admin" on "admin/sonata/news/comment/create?uniqid=4f155592a220e"
  And I fill in "4f155592a220e_name" with "toto"
  And I fill in "4f155592a220e_email" with "toto@ekino.com"
  And I fill in "4f155592a220e_message" with "comment"
  And I press "Create"
  Then I should see "Item has been successfully created."

Scenario: Export JSON data
  When I am connected with "admin" and "admin" on "admin/sonata/news/comment/list"
  And I follow "json"
  Then the response status code should be 200

Scenario: Export CSV data
  When I am connected with "admin" and "admin" on "admin/sonata/news/comment/list"
  And I follow "csv"
  Then the response status code should be 200

Scenario: Export XML data
  When I am connected with "admin" and "admin" on "admin/sonata/news/comment/list"
  And I follow "xml"
  Then the response status code should be 200

Scenario: Export XLS data
  When I am connected with "admin" and "admin" on "admin/sonata/news/comment/list"
  And I follow "xls"
  Then the response status code should be 200