@frontend
Feature: Check the page frontend

Scenario: Test page redirect blog => blog/archive
  When I go to "blog"
  Then I should see "Archive"