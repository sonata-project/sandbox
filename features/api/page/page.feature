@api @page
Feature: Check the Page controller calls for PageBundle

  Background:
    Given I am authenticating as "admin" with "admin" password

  # GET

  @api @page @list
  Scenario Outline: Get all pages
    When I send a GET request to "<resource>"
    Then the response code should be 200
    And response should contain "<format>" object
    And response pager should display page <page_number> with <per_page> elements
    And response pager data should be consistent

  Examples:
    | resource                                     | format | page_number | per_page |
    | /api/page/pages.xml                          | xml    | 1           | 10       |
    | /api/page/pages.xml?page=2&count=5           | xml    | 2           | 5        |
    | /api/page/pages.json                         | json   | 1           | 10       |
    | /api/page/pages.json?page=2&count=5          | json   | 2           | 5        |

  @api @page @unknown
  Scenario Outline: Get a specific page that not exists
    When I send a GET request to "/api/page/pages/99999999999.<format>"
    Then the response code should be 404
    And response should contain "<format>" object
    And response should contain "Page (99999999999) not found"

  Examples:
    | format  |
    | xml     |
    | json    |

  # POST

  @api @page @new @ko
  Scenario Outline: Post new page (with errors)
    When I send a POST request to "/api/page/pages.<format>" with values:
      | enabled     | 1         |
    Then the response code should be 400
    And response should contain "<format>" object
    And response should contain "Validation Failed"
    And response should contain "This value should not be null"

  Examples:
    | format  |
    | xml     |
    | json    |

  @api @page @workflow
  Scenario Outline: Page full workflow
    When I send a POST request to "/api/page/pages.<format>" with values:
      | name         | My other page       |
      | title        | My other page title |
      | enabled      | 1                   |
      | parent       | 1                   |
      | templateCode | default             |
      | position     | 1                   |
      | site         | 1                   |
    Then the response code should be 200
    And response should contain "<format>" object
    Then response should contain "created_at"
    Then store the <format> response identifier as "page_id"

    When I send a GET request to "/api/page/pages/<page_id>.<format>" using last identifier:
    Then the response code should be 200
    And response should contain "<format>" object
    And response should contain "My other page"
    And response should contain "My other page title"
    And response should contain "my-other-page"

    # PUT

    When I send a PUT request to "/api/page/pages/<page_id>.<format>" using last identifier with values:
      | name         | My new other page       |
      | title        | My new other page title |
      | enabled      | 1                       |
      | parent       | 1                       |
      | templateCode | default                 |
      | position     | 1                       |
      | site         | 1                       |
    Then the response code should be 200
    And response should contain "<format>" object
    Then response should contain "My new other page"
    And response should contain "My new other page title"
    And response should contain "my-new-other-page"

    When I send a GET request to "/api/page/pages/<page_id>.<format>" using last identifier:
    Then the response code should be 200
    And response should contain "<format>" object
    And response should contain "My new other page"
    And response should contain "My new other page title"

    # BLOCK

    When I send a POST request to "/api/page/pages/<page_id>/blocks.<format>" using last identifier with values:
      | name         | My other block            |
      | type         | sonata.block.service.text |
      | enabled      | 1                         |
      | position     | 1                         |
    Then the response code should be 200
    And response should contain "<format>" object
    Then response should contain "My other block"
    And response should contain "sonata.block.service.text"
    And store the <format> response identifier as "block_id"

    When I send a GET request to "/api/page/blocks/<block_id>.<format>" using last identifier:
    Then the response code should be 200
    And response should contain "<format>" object
    And response should contain "My other block"
    And response should contain "sonata.block.service.text"

    When  I send a PUT request to "/api/page/blocks/<block_id>.<format>" using last identifier with values:
      | name         | My other new block name   |
      | type         | sonata.block.service.text |
      | enabled      | 1                         |
      | position     | 1                         |
      | page         | <page_id>                 |
    Then the response code should be 200
    And response should contain "<format>" object
    And response should contain "My other new block name"
    And response should contain "sonata.block.service.text"

    When I send a DELETE request to "/api/page/blocks/<block_id>.<format>" using last identifier:
    Then the response code should be 200
    And response should contain "<format>" object
    And response should contain "true"

    When I send a GET request to "/api/page/blocks/<block_id>.<format>" using last identifier:
    Then the response code should be 404
    And response should contain "<format>" object

    # SNAPSHOT

    When I send a POST request to "/api/page/pages/<page_id>/snapshots.<format>" using last identifier:
    Then the response code should be 200
    And response should contain "<format>" object
    And response should contain "true"

    # DELETE

    When I send a DELETE request to "/api/page/pages/<page_id>.<format>" using last identifier:
    Then the response code should be 200
    And response should contain "<format>" object
    And response should contain "true"

    When I send a GET request to "/api/page/pages/<page_id>.<format>" using last identifier:
    Then the response code should be 404
    And response should contain "<format>" object

  Examples:
    | format  |
    | xml     |
    | json    |
