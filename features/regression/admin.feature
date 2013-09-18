@regression
Feature: Make sure there is no regression

Scenario: Make sure we can create sub class
  When I am connected with "admin" and "admin" on "/admin/sonata/demo/car/create?subclass=renault"
  Then I should see "renault"