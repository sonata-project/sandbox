@frontend @page
Feature: Check the default behavior of frontend pages
    In order to be able to buy some products
    As a customer
    I need to be able to use the shop

    Background:
        Given I am on "/"

    @200 @redirect
    Scenario: Test page redirect blog => blog/archive
        When I go to "blog"
        Then I should see "Archive"
        And I am on "/blog/archive"

    @redirect
    Scenario: Intercept redirect if connected as admin
        When I am connected with "admin" and "admin" on "admin/dashboard"
        And I go to "blog"
        Then I should see "Internal page redirection"
        And I should see "Please click here to follow the redirection"