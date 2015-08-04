@regression
Feature: Make sure there is no regression

Scenario: Make sure a subrequest can resolve the current selected website
  When I go to "/qa/page/controller-helper"
  Then I should see "The sub request current site name is: localhost"

Scenario: Make sure a subrequest can resolve the current selected website
  When I go to "/sub-site/qa/page/controller-helper"
  Then I should see "The sub request current site name is: sub site"

Scenario:
  When I go to "/_fragment?_path=pathInfo%3D%252Fsub-site%252Fqa%252Fpage%252Fcontroller-helper%26_format%3Dhtml%26_controller%3DSonataQABundle%253APage%253AinternalController&_hash=TVkI%2FF%2BwABMzAr6vo%2FCYm0IfBCKcGv9XguFf4N7FyrE%3D"
  Then I should see "The sub request current site name is: sub site"