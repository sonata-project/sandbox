@api @page @site
Feature: Check the Site controller calls for PageBundle

  Background:
    Given I am authenticating as "admin" with "admin" password

  # GET

  @api @page @site @list
  Scenario Outline: Get all sites
    When I send a GET request to "<resource>"
    Then the response code should be 200
    And response should contain "<format>" object
    And response pager should display page <page_number> with <per_page> elements
    And response pager data should be consistent

  Examples:
  | resource                                     | format | page_number | per_page |
  | /api/page/sites.xml                          | xml    | 1           | 10       |
  | /api/page/sites.xml?page=1&count=5           | xml    | 1           | 5        |
  | /api/page/sites.json                         | json   | 1           | 10       |
  | /api/page/sites.json?page=1&count=5          | json   | 1           | 5        |

  @api @page @site @unknown
  Scenario Outline: Get a specific site that not exists
    When I send a GET request to "/api/page/sites/99999999999.<format>"
    Then the response code should be 404
    And response should contain "<format>" object
    And response should contain "Site (99999999999) not found"

  Examples:
    | format  |
    | xml     |
    | json    |

  # POST

  @api @page @site @new @ko
  Scenario Outline: Post new site (with errors)
    When I send a POST request to "/api/page/sites.<format>" with values:
      | enabled     | 1         |
    Then the response code should be 400
    And response should contain "<format>" object
    And response should contain "Validation Failed"
    And response should contain "This value should not be null"

  Examples:
    | format  |
    | xml     |
    | json    |

  @api @page @site @workflow
  Scenario Outline: Site full workflow
    When I send a POST request to "/api/page/sites.<format>" with values:
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
    And response should contain "<format>" object
    And response should contain "created_at"
    And store the <format> response identifier as "site_id"

    When I send a GET request to "/api/page/sites/<site_id>.<format>" using last identifier:
    Then the response code should be 200
    And response should contain "<format>" object
    And response should contain "my site"
    And response should contain "My Site"

    # PUT

    When I send a PUT request to "/api/page/sites/<site_id>.<format>" using last identifier with values:
      | name        | my new site |
      | title       | My New Site |
      | enabled     | 1           |
    Then the response code should be 200
    And response should contain "<format>" object
    And response should contain "my new site"
    And response should contain "My New Site"

    When I send a GET request to "/api/page/sites/<site_id>.<format>" using last identifier:
    Then the response code should be 200
    And response should contain "<format>" object
    And response should contain "my new site"
    And response should contain "My New Site"

    # DELETE

    When I send a DELETE request to "/api/page/sites/<site_id>.<format>" using last identifier:
    Then the response code should be 200
    And response should contain "<format>" object
    And response should contain "true"

    When I send a GET request to "/api/page/sites/<site_id>.<format>" using last identifier:
    Then the response code should be 404
    And response should contain "<format>" object

  Examples:
    | format  |
    | xml     |
    | json    |
