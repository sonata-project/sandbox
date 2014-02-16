@frontend
Feature: Edit profile information

Scenario: Connect as user
  When I am connected with "johndoe" and "johndoe" on "login"
  And I go to "profile/"
  Then I should see "Dashboard"

Scenario: Edit my information
  When I am connected with "johndoe" and "johndoe" on "login"
  And I go to "profile/edit-profile"
  Then I should see "User Profile - Edit"
  When I fill in "sonata_user_profile_form_gender" with "m"
  And I fill in "sonata_user_profile_form_firstname" with "Thomas"
  And I fill in "sonata_user_profile_form_lastname" with "Rabaix"
  And I fill in "sonata_user_profile_form_website" with "http://rabaix.net"
  And I fill in "sonata_user_profile_form_biography" with "Sonata Core Dev"
  And I fill in "sonata_user_profile_form_locale" with "fr"
  And I fill in "sonata_user_profile_form_timezone" with "Europe/Paris"
  And I press "Submit"
  Then I should see "Your profile has been updated."