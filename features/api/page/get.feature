@api @get @page
Feature: Check the GET API calls for PageBundle

  Scenario: Check page list (json)
    Given I am authenticating as "admin" with "admin" password
    When  I send a GET request to "/api/page/pages.json"
    Then  the response code should be 200
    And   the response should contain json
    Then  response should contain "decorate"

  Scenario: Check page list (xml)
    Given I am authenticating as "admin" with "admin" password
    When  I send a GET request to "/api/page/pages.xml"
    Then  the response code should be 200
    And   the response should contain XML
    Then  response should contain "decorate"

  Scenario: Check unique page (json)
    Given I am authenticating as "admin" with "admin" password
    When  I send a GET request to "/api/page/pages/1.json"
    Then  the response code should be 200
    And   the response should contain json
    Then  response should contain "template_code"

  Scenario: Check unique page (xml)
    Given I am authenticating as "admin" with "admin" password
    When  I send a GET request to "/api/page/pages/1.xml"
    Then  the response code should be 200
    And   the response should contain XML
    Then  response should contain "template_code"

  Scenario: Check page pageblocks (json)
    Given I am authenticating as "admin" with "admin" password
    When  I send a GET request to "/api/page/pages/1/pageblocks.json"
    Then  the response code should be 200
    And   the response should contain json
    Then  response should contain "settings"

  Scenario: Check page pageblocks (xml)
    Given I am authenticating as "admin" with "admin" password
    When  I send a GET request to "/api/page/pages/1/pageblocks.xml"
    Then  the response code should be 200
    And   the response should contain XML
    Then  response should contain "settings"
