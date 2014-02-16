@backend @login
Feature: Check login
    In order to log into the administration panel 
    As an authenticated user
    I need to have an account

    @200
    Scenario: Check page accessibility
        Given I am on "/"
        When I go to "admin/dashboard"
        Then the response status code should be 200
        And the response should contain "Username"
        And I should be on "admin/login"

    Scenario Outline: Check authentication with different couples of login / pwd
        Given I am on "admin/login"
        And I fill in "_username" with "<username>"
        And I fill in "_password" with "<password>"
        And I press "_submit"
        Then the response should contain "<message>"

        Examples:
            | username |    password    | message |
            |   admin  |    test    | Bad credentials |
            |   admin  |    admin   | Welcome   |

    Scenario: Check user logout action
        Given I am connected with "admin" and "admin" on "admin/login"
        When I go to "admin/dashboard"
        And I follow "Logout"
        Then I should see "Welcome"
        And I should see "Sonata sandbox"

    Scenario Outline: Check menu items present on the dashboard
        Given I am connected with "admin" and "admin" on "admin/dashboard"
        Then the response should contain "<message>"

        Examples:
            | message |
            | Customer |
            | Invoice |
            | Order |
            | Product |
            | Category |

