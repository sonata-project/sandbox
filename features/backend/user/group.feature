@backend
Feature: Check the user admin module

  Scenario: Check group admin pages when not connected
    When I go to "admin/app/user-group/list"
    Then the response status code should be 200
    And I should see "Authentication"

  Scenario: Check group admin pages when connected
    When I am connected with "admin" and "admin" on "admin/app/user-group/list"
    Then I should see "Filters"

  Scenario: Add a new group with some errors
    When I am connected with "admin" and "admin" on "admin/app/user-group/create?uniqid=f155592a220e"
    And I press "Create"
    Then I should see "An error has occurred during the creation of item \"\"."

  @keep
  Scenario: Add a new group
    When I am connected with "admin" and "admin" on "admin/app/user-group/create?uniqid=f155592a220e"
    And I fill in "f155592a220e_name" with "toto"
    And I press "Create"
    Then I should see "Item \"toto\" has been successfully created."

  @keep
  Scenario: Filter groups
    When I am connected with "admin" and "admin" on "admin/app/user-group/list"
    And I fill in "filter_name_value" with "toto"
    And I press "Filter"
    Then I should see "toto"

  @keep
  Scenario: View revisions of a group
    When I am connected with "admin" and "admin" on "admin/app/user-group/list"
    And I follow "toto"
    And I follow "Revisions"
    Then the response status code should be 200

  @keep
  Scenario: Edit a group
    When I am connected with "admin" and "admin" on "admin/app/user-group/list"
    And I follow "toto"
    And I press "Update"
    Then I should see "Item \"toto\" has been successfully updated."

  @keep
  Scenario: Delete a group
    When I am connected with "admin" and "admin" on "admin/app/user-group/list"
    And I fill in "filter_name_value" with "toto"
    And I press "Filter"
    And I follow "toto"
    And I follow link "Delete" with class "btn btn-danger"
    And I press "Yes, delete"
    Then I should see "Item \"toto\" has been deleted successfully."



