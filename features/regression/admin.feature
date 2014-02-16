@regression
Feature: Make sure there is no regression

Scenario: Make sure we can create sub class
  Given I am connected with "admin" and "admin" on "/"
  When I go to "/admin/sonata/demo/car/create?subclass=renault" 
  Then the response should contain "Renault"
