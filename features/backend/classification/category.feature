@backend @category
Feature: Check the category admin module

  Scenario: Check category admin pages when not connected
    When I go to "admin/app/classification-category/list"
    Then the response status code should be 200
    And I should see "Authentication"

  Scenario: Check category admin pages when connected
    When I am connected with "admin" and "admin" on "admin/app/classification-category/list"
    Then I should see "Filters"

  Scenario: Add a new category with some errors
    When I am connected with "admin" and "admin" on "admin/app/classification-category/create?uniqid=f155592a220e"
    And I press "Create"
    Then I should see "An error has occurred during the creation of item \"n/a\"."

  Scenario: Add a new category
    When I am connected with "admin" and "admin" on "admin/app/classification-category/create?uniqid=f155592a220e"
    And I fill in "f155592a220e_name" with "toto"
    And I fill in "f155592a220e_parent" with "1"
    And I press "Create"
    Then I should see "Item \"toto\" has been successfully created."

  @keep
  Scenario: Filter categories
    When I am connected with "admin" and "admin" on "admin/app/classification-category/list"
    And I fill in "filter_name_value" with "toto"
    And I press "Filter"
    Then I should see "toto"

  @keep
  Scenario: Edit a category
    When I am connected with "admin" and "admin" on "admin/app/classification-category/list"
    And I follow "toto"
    And I press "Update"
    Then I should see "Item \"toto\" has been successfully updated."

  @keep
  Scenario: View history of a category
    When I am connected with "admin" and "admin" on "admin/app/classification-category/list"
    And I follow link "Filters" with class "dropdown-toggle sonata-ba-action"
    And I follow link "Name" with class "sonata-toggle-filter sonata-ba-action"
    And I fill in "filter_name_value" with "toto"
    And I press "Filter"
    And I follow "toto"
    And I follow "Revisions"
    Then the response status code should be 200

  @keep
  Scenario: Delete a category
    When I am connected with "admin" and "admin" on "admin/app/classification-category/list"
    And I follow link "Filters" with class "dropdown-toggle sonata-ba-action"
    And I follow link "Name" with class "sonata-toggle-filter sonata-ba-action"
    And I fill in "filter_name_value" with "toto"
    And I press "Filter"
    And I follow "toto"
    And I follow link "Delete" with class "btn btn-danger"
    And I press "Yes, delete"
    Then I should see "Item \"toto\" has been deleted successfully."

  Scenario: Export JSON data
    When I am connected with "admin" and "admin" on "admin/app/classification-category/list"
    And I follow link "List" with class "btn btn-default"
    And I follow "JSON"
    Then the response status code should be 200

  Scenario: Export CSV data
    When I am connected with "admin" and "admin" on "admin/app/classification-category/list"
    And I follow link "List" with class "btn btn-default"
    And I follow "CSV"
    Then the response status code should be 200

  Scenario: Export XML data
    When I am connected with "admin" and "admin" on "admin/app/classification-category/list"
    And I follow link "List" with class "btn btn-default"
    And I follow "XML"
    Then the response status code should be 200

  Scenario: Export XLS data
    When I am connected with "admin" and "admin" on "admin/app/classification-category/list"
    And I follow link "List" with class "btn btn-default"
    And I follow "XLS"
    Then the response status code should be 200