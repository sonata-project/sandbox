@frontend
Feature: Check the blog frontend

Scenario: Check blog post list status code
  When I go to "blog/archive"
  Then the response status code should be 200