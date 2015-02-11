@api @pge @site
Feature: Check the Site controller calls for PageBundle

  Background:
    Given I am authenticating as "admin" with "admin" password

  # GET

  Scenario: Get all sites
    When I send a GET request to "/api/page/sites.xml"
    Then the response code should be 200
    And response should contain "xml" object
    And response should contain "page"
    And response should contain "entries"

  # POST

  Scenario: Post new site (with errors)
    When I send a POST request to "/api/page/sites.xml" with values:
      | enabled     | 1         |
    Then the response code should be 400
    And response should contain "xml" object
    And response should contain "Validation Failed"
    And response should contain "This value should not be null"

  Scenario: Site full workflow
    When I send a POST request to "/api/page/sites.xml" with values:
      | name            | my site             |
      | host            | localhost           |
      | enabled         | 1                   |
      | relativePath    | 1                   |
      | enabledFrom     | 2015-01-01 00:00:00 |
      | enabledTo       | 2019-01-01 00:00:00 |
      | isDefault       | 1                   |
      | locale          | en                  |
      | title           | My Site             |
      | metaKeywords    | keyword             |
      | metaDescription | description         |
    Then  the response code should be 200
    And response should contain "xml" object
    And response should contain "created_at"
    And store the XML response identifier as "site_id"

    When I send a GET request to "/api/page/sites/<site_id>.xml" using last identifier:
    Then the response code should be 200
    And response should contain "xml" object
    And response should contain "my site"
    And response should contain "My Site"

    # PUT

    When I send a PUT request to "/api/page/sites/<site_id>.xml" using last identifier with values:
      | name        | my new site |
      | title       | My New Site |
      | enabled     | 1           |
    Then the response code should be 200
    And response should contain "xml" object
    And response should contain "my new site"
    And response should contain "My New Site"

    When I send a GET request to "/api/page/sites/<site_id>.xml" using last identifier:
    Then the response code should be 200
    And response should contain "xml" object
    And response should contain "my new site"
    And response should contain "My New Site"

    # DELETE

    When I send a DELETE request to "/api/page/sites/<site_id>.xml" using last identifier:
    Then the response code should be 200
    And response should contain "xml" object
    And response should contain "true"

    When I send a GET request to "/api/page/sites/<site_id>.xml" using last identifier:
    Then the response code should be 404
    And response should contain "xml" object
