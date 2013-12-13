@frontend
Feature: Check the page frontend

Scenario: Test page redirect blog => blog/archive
  When I go to "blog"
  Then I should see "Archive"

Scenario: Intercept redirect if connected as admin
  When I am connected with "admin" and "admin" on "admin/dashboard"
  And I go to "blog"
  Then I should see "Internal page redirection"
  And I should see "Please click here to follow the redirection"
