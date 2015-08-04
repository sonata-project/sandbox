@backend
Feature: Check the comment admin module

  Scenario: Check comment admin pages when not connected
    When I go to "admin/app/news-comment/list"
    Then the response status code should be 200
    And I should see "Authentication"

  Scenario: Check comment admin pages when connected
    When I am connected with "admin" and "admin" on "admin/app/news-comment/list"
    Then I should see "Filters"

  Scenario: Add a new comment with some errors
    When I am connected with "admin" and "admin" on "admin/app/news-comment/create?uniqid=f155592a220e"
    And I press "Create"
    Then I should see "An error has occurred during the creation of item \"n-a\"."

  Scenario: Export JSON data
    When I am connected with "admin" and "admin" on "admin/app/news-comment/list"
    And I follow "JSON"
    Then the response status code should be 200

  Scenario: Export CSV data
    When I am connected with "admin" and "admin" on "admin/app/news-comment/list"
    And I follow "CSV"
    Then the response status code should be 200

  Scenario: Export XML data
    When I am connected with "admin" and "admin" on "admin/app/news-comment/list"
    And I follow "XML"
    Then the response status code should be 200

  Scenario: Export XLS data
    When I am connected with "admin" and "admin" on "admin/app/news-comment/list"
    And I follow "XLS"
    Then the response status code should be 200

  @keep
  Scenario: Add a new comment
    When I am connected with "admin" and "admin" on "admin/app/news-comment/create?uniqid=f155592a220e"
    And I fill in "f155592a220e_name" with "toto"
    And I fill in "f155592a220e_email" with "toto@ekino.com"
    And I fill in "f155592a220e_message" with "comment"
    And I fill in "f155592a220e_post" with "1"
    And I press "Create"
    Then I should see "Item \"toto\" has been successfully created."

  @keep
  Scenario: Filter comments
    When I am connected with "admin" and "admin" on "admin/app/news-comment/list"
    And I fill in "filter_name_value" with "toto"
    And I press "Filter"
    Then I should see "toto"

  @keep
  Scenario: Edit a comment
    When I am connected with "admin" and "admin" on "admin/app/news-comment/list"
    And I fill in "filter_name_value" with "toto"
    And I press "Filter"
    And I follow "toto"
    And I press "Update"
    Then I should see "Item \"toto\" has been successfully updated."

  @keep
  Scenario: View history of a comment
    When I am connected with "admin" and "admin" on "admin/app/news-comment/list"
    And I fill in "filter_name_value" with "toto"
    And I press "Filter"
    And I follow "toto"
    And I follow "Revisions"
    Then the response status code should be 200

  @keep
  Scenario: Delete a comment
    When I am connected with "admin" and "admin" on "admin/app/news-comment/list"
    And I fill in "filter_name_value" with "toto"
    And I press "Filter"
    And I follow "toto"
    And I follow link "Delete" with class "btn btn-danger"
    And I press "Yes, delete"
    Then I should see "Item \"toto\" has been deleted successfully."
