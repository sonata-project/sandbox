@api @ecommerce @product
Feature: Check the Product controller calls for ProductBundle

  Background:
    Given I am authenticating as "admin" with "admin" password

  # GET

  Scenario: Check product list
    When I send a GET request to "/api/ecommerce/products.xml"
    Then the response code should be 200
    And response should contain "xml" object
    And response should contain "price_including_vat"

  Scenario: Check unique product
    When I send a GET request to "/api/ecommerce/products/1.xml"
    Then the response code should be 200
    And response should contain "xml" object
    And response should contain "price_including_vat"

  Scenario: Check product categories
    When I send a GET request to "/api/ecommerce/products/1/categories.xml"
    Then the response code should be 200
    And response should contain "xml" object
    And response should contain "description"

  Scenario: Check product collections
    When I send a GET request to "/api/ecommerce/products/1/collections.xml"
    Then the response code should be 200
    And response should contain "xml" object
    And response should contain "description"

  Scenario: Check product deliveries
    When I send a GET request to "/api/ecommerce/products/1/deliveries.xml"
    Then the response code should be 200
    And response should contain "xml" object
    And response should contain "product_id"

  Scenario: Check product packages
    When I send a GET request to "/api/ecommerce/products/1/packages.xml"
    Then the response code should be 200
    And response should contain "xml" object
    And response should contain "product_id"

  Scenario: Check product productcategories
    When I send a GET request to "/api/ecommerce/products/1/productcategories.xml"
    Then the response code should be 200
    And response should contain "xml" object
    And response should contain "category_id"

  Scenario: Check product productcollections
    When I send a GET request to "/api/ecommerce/products/1/productcollections.xml"
    Then the response code should be 200
    And response should contain "xml" object
    And response should contain "collection_id"

  Scenario: Check product variations
    When I send a GET request to "/api/ecommerce/products/524/variations.xml"
    Then the response code should be 200
    And response should contain "xml" object
    And response should contain "travellers"

  # POST

  Scenario: Post new product (with errors)
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