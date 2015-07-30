@backend
Feature: Check the car admin module

  Scenario: Check comment admin pages when not connected
    When I go to "admin/sonata/demo/car/list"
    Then the response status code should be 200
    And I should see "Authentication"

  Scenario: Check car admin pages when connected
    When I am connected with "admin" and "admin" on "admin/sonata/demo/car/list"
    Then I should see "Filters"

  Scenario: Add a new car with some errors
    When I am connected with "admin" and "admin" on "admin/sonata/demo/car/create?subclass=renault&uniqid=f155592a220e"
    And I press "Create"
    Then I should see "An error has occurred during the creation of item \"Renault\"."

  Scenario: Add a new car
    When I am connected with "admin" and "admin" on "admin/sonata/demo/car/create?subclass=renault&uniqid=f155592a220e"
    And I fill in "Name" with "toto"
    And I fill in "f155592a220e_inspections_0_date" with "2013-01-01"
    And I press "Create"
    Then I should see "Item \"Renault\" has been successfully created."

  @keep
  Scenario: Edit a car
    When I am connected with "admin" and "admin" on "admin/sonata/demo/car/list"
    And I follow "toto"
    And I press "Update"
    Then I should see "Item \"Renault\" has been successfully updated."

  @keep
  Scenario: Filter cars
    When I am connected with "admin" and "admin" on "admin/sonata/demo/car/list"
    And I fill in "filter_name_value" with "toto"
    And I press "Filter"
    Then I should see "name"

  @keep
  Scenario: Delete a car
    When I am connected with "admin" and "admin" on "admin/sonata/demo/car/list"
    And I fill in "filter_name_value" with "toto"
    And I press "Filter"
    And I follow "toto"
    And I follow link "Delete" with class "btn btn-danger"
    And I press "Yes, delete"
    Then I should see "Item \"Renault\" has been deleted successfully."

  Scenario: Export JSON data
    When I am connected with "admin" and "admin" on "admin/sonata/demo/car/list"
    And I should not see "JSON"

  Scenario: Export CSV data
    When I am connected with "admin" and "admin" on "admin/sonata/demo/car/list"
    And I should not see "CSV"

  Scenario: Export XML data
    When I am connected with "admin" and "admin" on "admin/sonata/demo/car/list"
    And I should not see "XML"

  Scenario: Export XLS data
    When I am connected with "admin" and "admin" on "admin/sonata/demo/car/list"
    And I should not see "XLS"