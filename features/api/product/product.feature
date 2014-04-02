@api @ecommerce @product
Feature: Check the Product controller calls for ProductBundle
  I want to test different API calls

  Background:
    Given I am authenticating as "admin" with "admin" password

  # GET

  @api @product @list
  Scenario Outline: Retrieve all available products
    When I send a GET request to "<resource>"
    Then the response code should be <status_code>
    And response should contain "<format>" object
    And response should contain "<message>"

    Examples:
      | resource | status_code | format | message |
      | /api/ecommerce/products.json | 200 | json | price_including_vat |
      | /api/ecommerce/products.xml  | 200 | xml  | price_including_vat |

  @api @product @id
  Scenario Outline: Retrieve a specific product by an unique ID
    When I send a GET request to "<resource>"
    Then the response code should be <status_code>
    And response should contain "<format>" object
    And response should contain "<message>"

    Examples:
      | resource | status_code | format | message |
      | /api/ecommerce/products/1.json | 200 | json | price_including_vat |
      | /api/ecommerce/products/1.xml  | 200 | xml  | price_including_vat |

  @api @product @categories @id @list
  Scenario Outline: Retrieve all categories for a specific product identified by an unique ID
    When I send a GET request to "<resource>"
    Then the response code should be <status_code>
    And response should contain "<format>" object
    And response should contain "<message>"

    Examples:
      | resource | status_code | format | message |
      | /api/ecommerce/products/1/categories.json | 200 | json | description |
      | /api/ecommerce/products/1/categories.xml  | 200 | xml  | description |

  @api @product @collections @id @list
  Scenario Outline: Retrieve all collections for a specific product identified by an unique ID
    When I send a GET request to "<resource>"
    Then the response code should be <status_code>
    And response should contain "<format>" object
    And response should contain "<message>"

    Examples:
      | resource | status_code | format | message |
      | /api/ecommerce/products/1/collections.json | 200 | json | description |
      | /api/ecommerce/products/1/collections.xml  | 200 | xml  | description |

  @api @product @deliveries @id @list
  Scenario Outline: Retrieve all deliveries for a specific product identified by an unique ID
    When I send a GET request to "<resource>"
    Then the response code should be <status_code>
    And response should contain "<format>" object
    And response should contain "<message>"

    Examples:
      | resource | status_code | format | message |
      | /api/ecommerce/products/1/deliveries.json | 200 | json | product_id |
      | /api/ecommerce/products/1/deliveries.xml  | 200 | xml  | product_id |

  @api @product @packages @id @list
  Scenario Outline: Retrieve all packages for a specific product identified by an unique ID
    When I send a GET request to "<resource>"
    Then the response code should be <status_code>
    And response should contain "<format>" object
    And response should contain "<message>"

    Examples:
      | resource | status_code | format | message |
      | /api/ecommerce/products/1/packages.json | 200 | json | product_id |
      | /api/ecommerce/products/1/packages.xml  | 200 | xml  | product_id |

  @api @product @productcategories @id @list
  Scenario Outline: Retrieve all productcategories for a specific product identified by an unique ID
    When I send a GET request to "<resource>"
    Then the response code should be <status_code>
    And response should contain "<format>" object
    And response should contain "<message>"

    Examples:
      | resource | status_code | format | message |
      | /api/ecommerce/products/1/productcategories.json | 200 | json | category_id |
      | /api/ecommerce/products/1/productcategories.xml  | 200 | xml  | category_id |

  @api @product @productcollections @id @list
  Scenario Outline: Retrieve all productcollections for a specific product identified by an unique ID
    When I send a GET request to "<resource>"
    Then the response code should be <status_code>
    And response should contain "<format>" object
    And response should contain "<message>"

    Examples:
      | resource | status_code | format | message |
      | /api/ecommerce/products/1/productcollections.json | 200 | json | collection_id |
      | /api/ecommerce/products/1/productcollections.xml  | 200 | xml  | collection_id |

  @api @product @variations @id @list
  Scenario Outline: Retrieve all variations for a specific product identified by an unique ID
    When I send a GET request to "<resource>"
    Then the response code should be <status_code>
    And response should contain "<format>" object
    And response should contain "<message>"

    Examples:
      | resource | status_code | format | message |
      | /api/ecommerce/products/524/variations.json | 200 | json | travellers |
      | /api/ecommerce/products/524/variations.xml  | 200 | xml  | travellers |

  # POST

  @api @product @new @ko
  Scenario: Post new product with errors
    When I send a POST request to "/api/ecommerce/sonata.ecommerce_demo.product.travel/products.xml" with values:
      | sku               | TESTSKU0001     |
      | slug              | my-product-slug |
      | priceIncludingVat | 1               |
      | price             | 15.00           |
      | vatRate           | 20              |
      | enabled           | 1               |
    Then the response code should be 400
    And response should contain "xml" object
    And response should contain "Validation Failed"
    And response should contain "This value should not be null"

  @api @product @workflow
  Scenario: Product full workflow
    When I send a POST request to "/api/ecommerce/sonata.ecommerce_demo.product.travel/products.xml" with values:
      | sku               | TESTSKU0001     |
      | name              | My product slug |
      | slug              | my-product-slug |
      | priceIncludingVat | 1               |
      | price             | 15.00           |
      | vatRate           | 20              |
      | enabled           | 1               |
    Then the response code should be 200
    And response should contain "xml" object
    And response should contain "created_at"
    And store the XML response identifier as "product_id"

    When I send a GET request to "/api/ecommerce/products/<product_id>.xml" using last identifier:
    Then the response code should be 200
    And response should contain "xml" object
    And response should contain "TESTSKU0001"
    And response should contain "My product slug"
    And response should contain "my-product-slug"
    And response should contain "15"

    # PUT

    When I send a PUT request to "/api/ecommerce/sonata.ecommerce_demo.product.travel/products/<product_id>.xml" using last identifier with values:
      | sku               | TESTNEWSKU0001      |
      | name              | My new product slug |
      | slug              | my-new-product-slug |
      | priceIncludingVat | 1                   |
      | price             | 17.00               |
      | vatRate           | 20                  |
      | enabled           | 1                   |
    Then the response code should be 200
    And response should contain "xml" object
    And response should contain "TESTNEWSKU0001"
    And response should contain "My new product slug"
    And response should contain "my-new-product-slug"
    And response should contain "17"

    When I send a GET request to "/api/ecommerce/products/<product_id>.xml" using last identifier:
    Then the response code should be 200
    And response should contain "xml" object
    And response should contain "TESTNEWSKU0001"
    And response should contain "My new product slug"
    And response should contain "my-new-product-slug"
    And response should contain "17"

    # DELETE

    When I send a DELETE request to "/api/ecommerce/products/<product_id>.xml" using last identifier:
    Then the response code should be 200
    And response should contain "xml" object
    And response should contain "true"

    When I send a GET request to "/api/ecommerce/products/<product_id>.xml" using last identifier:
    Then the response code should be 404
    And response should contain "xml" object