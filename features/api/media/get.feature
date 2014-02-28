@api @get @media
Feature: Check the GET API calls for Media

  Scenario: Check media list (json)
    Given I am authenticating as "admin" with "admin" password
    When  I send a GET request to "/api/media/media.json"
    Then  the response code should be 200
    And   the response should contain json
    Then  response should contain "provider_metadata"

  Scenario: Check media list (xml)
    Given I am authenticating as "admin" with "admin" password
    When  I send a GET request to "/api/media/media.xml"
    Then  the response code should be 200
    And   the response should contain XML
    Then  response should contain "provider_metadata"

  Scenario: Check unique media (json)
    Given I am authenticating as "admin" with "admin" password
    When  I send a GET request to "/api/media/media/1.json"
    Then  the response code should be 200
    And   the response should contain json
    Then  response should contain "provider_metadata"

  Scenario: Check unique media (xml)
    Given I am authenticating as "admin" with "admin" password
    When  I send a GET request to "/api/media/media/1.xml"
    Then  the response code should be 200
    And   the response should contain XML
    Then  response should contain "provider_metadata"

  Scenario: Check media binaries (json)
    Given I am authenticating as "admin" with "admin" password
    When  I send a GET request to "/api/media/media/1/binaries.json"
    Then  the response code should be 404

  Scenario: Check media binaries (xml)
    Given I am authenticating as "admin" with "admin" password
    When  I send a GET request to "/api/media/media/1/binaries.xml"
    Then  the response code should be 404

  Scenario: Check media binaries (json)
    Given I am authenticating as "admin" with "admin" password
    When  I send a GET request to "/api/media/media/1/formats.json"
    Then  the response code should be 200
    And   the response should contain json

  Scenario: Check media binaries (xml)
    Given I am authenticating as "admin" with "admin" password
    When  I send a GET request to "/api/media/media/1/formats.xml"
    Then  the response code should be 200
    And   the response should contain XML


  Scenario: Check gallery list (json)
    Given I am authenticating as "admin" with "admin" password
    When  I send a GET request to "/api/media/galleries.json"
    Then  the response code should be 200
    And   the response should contain json
    Then  response should contain "Japan"

  Scenario: Check gallery list (xml)
    Given I am authenticating as "admin" with "admin" password
    When  I send a GET request to "/api/media/galleries.xml"
    Then  the response code should be 200
    And   the response should contain XML
    Then  response should contain "Japan"

  Scenario: Check unique gallery (json)
    Given I am authenticating as "admin" with "admin" password
    When  I send a GET request to "/api/media/galleries/1.json"
    Then  the response code should be 200
    And   the response should contain json
    Then  response should contain "context"

  Scenario: Check unique gallery (xml)
    Given I am authenticating as "admin" with "admin" password
    When  I send a GET request to "/api/media/galleries/1.xml"
    Then  the response code should be 200
    And   the response should contain XML
    Then  response should contain "context"

  Scenario: Check gallery galleryhasmedias (json)
    Given I am authenticating as "admin" with "admin" password
    When  I send a GET request to "/api/media/galleries/1/galleryhasmedias.json"
    Then  the response code should be 200
    And   the response should contain json
    Then  response should contain "gallery_id"

  Scenario: Check gallery galleryhasmedias (xml)
    Given I am authenticating as "admin" with "admin" password
    When  I send a GET request to "/api/media/galleries/1/galleryhasmedias.xml"
    Then  the response code should be 200
    And   the response should contain XML
    Then  response should contain "gallery_id"

  Scenario: Check gallery medias (json)
    Given I am authenticating as "admin" with "admin" password
    When  I send a GET request to "/api/media/galleries/1/medias.json"
    Then  the response code should be 200
    And   the response should contain json

  Scenario: Check gallery medias (xml)
    Given I am authenticating as "admin" with "admin" password
    When  I send a GET request to "/api/media/galleries/1/medias.xml"
    Then  the response code should be 200
    And   the response should contain XML
