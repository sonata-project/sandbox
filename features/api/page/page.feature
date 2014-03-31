@api @page
Feature: Check the Page controller calls for PageBundle

  Background:
    Given I am authenticating as "admin" with "admin" password

  # GET

  Scenario: Get all pages
    When I send a GET request to "/api/page/pages.xml"
    Then the response code should be 200
    And response should contain "xml" object
    And response should contain "global"
    And response should contain "Homepage"

  # POST

  Scenario: Page full workflow
    When I send a POST request to "/api/page/pages.xml" with values:
      | name         | My other page       |
      | title        | My other page title |
      | enabled      | 1                   |
      | parent       | 1                   |
      | templateCode | default             |
      | position     | 1                   |
      | site         | 1                   |
    Then the response code should be 200
    And response should contain "xml" object
    Then response should contain "created_at"
    Then store the XML response identifier as "page_id"

    When I send a GET request to "/api/page/pages/<page_id>.xml" using last identifier:
    Then the response code should be 200
    And response should contain "xml" object
    And response should contain "My other page"
    And response should contain "My other page title"
    And response should contain "my-other-page"

    # PUT

    When I send a PUT request to "/api/page/pages/<page_id>.xml" using last identifier with values:
      | name         | My new other page       |
      | title        | My new other page title |
      | enabled      | 1                       |
      | parent       | 1                       |
      | templateCode | default                 |
      | position     | 1                       |
      | site         | 1                       |
    Then the response code should be 200
    And response should contain "xml" object
    Then response should contain "My new other page"
    And response should contain "My new other page title"
    And response should contain "my-other-page"

    When I send a GET request to "/api/page/pages/<page_id>.xml" using last identifier:
    Then the response code should be 200
    And response should contain "xml" object
    And response should contain "My new other page"
    And response should contain "My new other page title"

    # BLOCK

    When I send a POST request to "/api/page/pages/<page_id>/blocks.xml" using last identifier with values:
      | name         | My other block            |
      | type         | sonata.block.service.text |
      | enabled      | 1                         |
      | position     | 1                         |
    Then the response code should be 200
    And response should contain "xml" object
    Then response should contain "My other block"
    And response should contain "sonata.block.service.text"
    And store the XML response identifier as "block_id"

    When I send a GET request to "/api/page/blocks/<block_id>.xml" using last identifier:
    Then the response code should be 200
    And response should contain "xml" object
    And response should contain "My other block"
    And response should contain "sonata.block.service.text"

    When  I send a PUT request to "/api/page/blocks/<block_id>.xml" using last identifier with values:
      | name         | My other new block name   |
      | type         | sonata.block.service.text |
      | enabled      | 1                         |
      | position     | 1                         |
      | page         | <page_id>                 |
    Then the response code should be 200
    And response should contain "xml" object
    And response should contain "My other new block name"
    And response should contain "sonata.block.service.text"

    When I send a DELETE request to "/api/page/blocks/<block_id>.xml" using last identifier:
    Then the response code should be 200
    And response should contain "xml" object
    And response should contain "true"

    When I send a GET request to "/api/page/blocks/<block_id>.xml" using last identifier:
    Then the response code should be 404
    And response should contain "xml" object

    # SNAPSHOT

    When I send a POST request to "/api/page/pages/<page_id>/snapshots.xml" using last identifier:
    Then the response code should be 200
    And response should contain "xml" object
    And response should contain "true"

    # DELETE

    When I send a DELETE request to "/api/page/pages/<page_id>.xml" using last identifier:
    Then the response code should be 200
    And response should contain "xml" object
    And response should contain "true"

    When I send a GET request to "/api/page/pages/<page_id>.xml" using last identifier:
    Then the response code should be 404
    And response should contain "xml" object