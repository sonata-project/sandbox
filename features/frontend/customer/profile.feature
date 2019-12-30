@frontend @customer @profile
Feature: Edit my profile
  In order to consult or modify my information
  As a customer
  I want to be able to connect to my personal account

  Background:
    Given I am connected with "johndoe" and "johndoe" on "login"
    And I am on "/"

  @200 @profile
  Scenario: Connect as customer
    When I go to "/profile"
    Then I should see "Dashboard"
    And I should see "Recent Orders"
    And the response status code should be 200

  @profile @customer @edition
  Scenario: Update my profile information
    When I go to "/profile/edit"
    And I fill in "fos_user_profile_form_username" with "johndoe"
    And I fill in "fos_user_profile_form_email" with "johndoe@example.net"
    And I fill in "fos_user_profile_form_current_password" with "johndoe"
    And I press "Update"
    Then I should see "The profile has been updated."

  @profile @customer @password
    Scenario: Change my password
    When I go to "/profile/change-password"
    And I fill in "fos_user_change_password_form_current_password" with "johndoe"
    And I fill in "fos_user_change_password_form_plainPassword_first" with "johndoe"
    And I fill in "fos_user_change_password_form_plainPassword_second" with "johndoe"
    And I press "Change password"
    Then I should see "The password has been changed."