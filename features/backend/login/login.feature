@backend
Feature: Check login

Scenario: Check login page when not connected
  When I go to "admin/login"
  Then the response status code should be 200
  And I should see "Username"

Scenario: Check user login page when connected
  When I am connected with "admin" and "admin" on "admin/dashboard"
  Then I should be on "admin/dashboard"
  And I should see "Dashboard"

Scenario: Check user logout action
  When I am connected with "admin" and "admin" on "admin/dashboard"
  Then I follow "Logout"
  And I should see "Welcome"