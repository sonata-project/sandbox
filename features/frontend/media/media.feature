@frontend @media
Feature: Check the Media / Gallery frontend
    In order to watch some medias
    As a visitor
    I want to be able to see the galleries

    @200
    Scenario: Check gallery list status code
        Given I am on the homepage
        When I go to "media/gallery"
        Then I should see "Gallery List"
        And the response status code should be 200

    @200
    Scenario: Check Media & SEO page status code
        Given I am on the homepage
        When I go to "media"
        Then I should see "Form Media Type & SEO"
        And the response status code should be 200

    Scenario: Watch medias from a selected gallery
        When I go to "media/gallery/view/1"
        Then the response should contain "sonata-media-gallery-media-list"
#        Then I should see an "sonata-media-gallery-media-list" element

    Scenario: Watch a selected media non authenticated
        When I go to "media/gallery/view/1"
        And I follow the first link of section "media sonata-media-gallery-media-item"
        Then I should see "Username"

    Scenario: Watch a selected media authenticated
        When I go to "media/gallery/view/1"
        And I follow the first link of section "media sonata-media-gallery-media-item"
        And I fill in "username" with "admin"
        And I fill in "password" with "admin"
        And I press "Login"
        Then the response should contain "Content Type"
        And the response should contain "Width"