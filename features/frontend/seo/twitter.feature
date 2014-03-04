@frontend
Feature: Test the page frontend SEO

Scenario: Twitter block is displayed on homepage
  Given I am on "/"
  Then I should see "The documentation of @sonataproject's @MongoDB Admin is now available on the official website"