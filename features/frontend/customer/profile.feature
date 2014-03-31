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
  Scenario: Update my personal information
    When I go to "/profile/edit-profile"
    And I fill in "sonata_user_profile_form_gender" with "m"
    And I fill in "sonata_user_profile_form_firstname" with "Thomas"
    And I fill in "sonata_user_profile_form_lastname" with "Rabaix"
    And I fill in "sonata_user_profile_form_dateOfBirth" with "1981-03-24"
    And I fill in "sonata_user_profile_form_website" with "http://thomas.rabaix.net"
    And I fill in "sonata_user_profile_form_biography" with "Sonata Core Dev"
    And I fill in "sonata_user_profile_form_locale" with "fr"
    And I fill in "sonata_user_profile_form_timezone" with "Europe/Paris"
    And I press "Submit"
    Then I should see "Your profile has been updated."

  @profile @customer @password
    Scenario: Change my password
    When I go to "/profile/edit-profile"
    And I fill in "fos_user_change_password_form_current_password" with "johndoe"
    And I fill in "fos_user_change_password_form_new_first" with "johndoe"
    And I fill in "fos_user_change_password_form_new_second" with "johndoe"
    And I press "Change password"
    Then I should see "The password has been changed"