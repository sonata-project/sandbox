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

Scenario: Make sure a subrequest can resolve the current selected website
  When I go to "/qa/page/controller-helper"
  Then I should see "The sub request current site name is: localhost"

Scenario: Make sure a subrequest can resolve the current selected website
  When I go to "/sub-site/qa/page/controller-helper"
  Then I should see "The sub request current site name is: sub site"

Scenario:
  When I go to "/_fragment?_path=pathInfo%3D%252Fsub-site%252Fqa%252Fpage%252Fcontroller-helper%26_format%3Dhtml%26_controller%3DSonataQABundle%253APage%253AinternalController&amp;_hash=%2FP1OvTzC3POAoWl%2FCWKDjb1aR10%3D"
  Then I should see "The sub request current site name is: sub site"