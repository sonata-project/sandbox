@api @ecommerce @product
Feature: Check the Product controller calls for ProductBundle

  Background:
    Given I am authenticating as "admin" with "admin" password

  # GET

  @api @product @list
  Scenario Outline: Retrieve all available products
    When I send a GET request to "<resource>"
    Then the response code should be <status_code>
    And response should contain "<format>" object
    And response pager should display page <page_number> with <per_page> elements
    And response pager data should be consistent

  Examples:
    | resource                                             | status_code  | format | page_number | per_page |
    | /api/ecommerce/products.xml                          | 200          | xml    | 1           | 10       |
    | /api/ecommerce/products.xml?page=1&count=5           | 200          | xml    | 1           | 5        |
    | /api/ecommerce/products.json                         | 200          | json   | 1           | 10       |
    | /api/ecommerce/products.json?page=1&count=5          | 200          | json   | 1           | 5        |

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

  @api @product @unknown
  Scenario Outline: Check unavailable unique product
    When I send a GET request to "<resource>"
    Then the response code should be <status_code>
    And response should contain "<format>" object
    And response should contain "<message>"

  Examples:
    | resource                                | status_code | format | message                       |
    | /api/ecommerce/products/9999999999.json | 404         | json   | Product (9999999999) not found |
    | /api/ecommerce/products/9999999999.xml  | 404         | xml    | Product (9999999999) not found |

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

  # tests are not save as they rely on non unique id ...
  #@api @product @variations @id @list
  #Scenario Outline: Retrieve all variations for a specific product identified by an unique ID
  #  When I send a GET request to "<resource>"
  #  Then the response code should be <status_code>
  #  Then print response
  #  And response should contain "<format>" object
  #  And response should contain "<message>"

  #  Examples:
  #    | resource | status_code | format | message |
  #    | /api/ecommerce/products/524/variations.json | 200 | json | travellers |
  #    | /api/ecommerce/products/524/variations.xml  | 200 | xml  | travellers |

  # POST

  @api @product @new @ko
  Scenario Outline: Post new product with errors
    When I send a POST request to "/api/ecommerce/<provider>/products.<format>" with values:
      | sku               | TESTSKU0001     |
      | slug              | my-product-slug |
      | priceIncludingVat | 1               |
      | price             | 15.00           |
      | vatRate           | 20              |
      | enabled           | 1               |
    Then the response code should be 400
    And response should contain "<format>" object
    And response should contain "Validation Failed"
    And response should contain "This value should not be null"

    Examples:
      | format | provider                             |
      | xml    | sonata.ecommerce_demo.product.travel |
      | json   | sonata.ecommerce_demo.product.travel |

  Scenario Outline: Post new product with bad provider
    When I send a POST request to "/api/ecommerce/<provider>/products.<format>" with values:
      | sku               | TESTSKU0001     |
      | slug              | my-product-slug |
      | priceIncludingVat | 1               |
      | price             | 15.00           |
      | vatRate           | 20              |
      | enabled           | 1               |
    Then the response code should be 404
    And response should contain "<format>" object
    And response should contain "The product definition `<provider>` does not exist!"

    Examples:
      | format  | provider                            |
      | xml     | sonata.ecommerce_demo.product.bad   |
      | json    | sonata.ecommerce_demo.product.bad   |

  @api @product @workflow
  Scenario Outline: Product full workflow
    When I send a POST request to "/api/ecommerce/<provider>/products.<format>" with values:
      | sku                       | TESTSKU0001           |
      | name                      | My product slug       |
      | slug                      | my-product-slug       |
      | descriptionFormatter      | markdown              |
      | rawDescription            | **description**       |
      | shortDescriptionFormatter | markdown              |
      | rawShortDescription       | **short description** |
      | priceIncludingVat         | 1                     |
      | price                     | 15.00                 |
      | vatRate                   | 20                    |
      | enabled                   | 1                     |
    Then the response code should be 200
    And response should contain "<format>" object
    And response should contain "created_at"
    And store the <format> response identifier as "product_id"

    When I send a GET request to "/api/ecommerce/products/<product_id>.<format>" using last identifier:
    Then the response code should be 200
    And response should contain "<format>" object
    And response should contain "TESTSKU0001"
    And response should contain "My product slug"
    And response should contain "my-product-slug"
    And response should contain "15"

    # PUT

    When I send a PUT request to "/api/ecommerce/<provider>/products/<product_id>.<format>" using last identifier with values:
      | sku                       | TESTNEWSKU0001            |
      | name                      | My new product slug       |
      | slug                      | my-new-product-slug       |
      | descriptionFormatter      | markdown                  |
      | rawDescription            | **new description**       |
      | shortDescriptionFormatter | markdown                  |
      | rawShortDescription       | **new short description** |
      | priceIncludingVat         | 1                         |
      | price                     | 17.00                     |
      | vatRate                   | 20                        |
      | enabled                   | 1                         |
    Then the response code should be 200
    And response should contain "<format>" object
    And response should contain "TESTNEWSKU0001"
    And response should contain "My new product slug"
    And response should contain "my-new-product-slug"
    And response should contain "17"

    When I send a GET request to "/api/ecommerce/products/<product_id>.<format>" using last identifier:
    Then the response code should be 200
    And response should contain "<format>" object
    And response should contain "TESTNEWSKU0001"
    And response should contain "My new product slug"
    And response should contain "my-new-product-slug"
    And response should contain "17"

    # DELETE

    When I send a DELETE request to "/api/ecommerce/products/<product_id>.<format>" using last identifier:
    Then the response code should be 200
    And response should contain "<format>" object
    And response should contain "true"

    When I send a GET request to "/api/ecommerce/products/<product_id>.<format>" using last identifier:
    Then the response code should be 404
    And response should contain "<format>" object

    Examples:
      | format  | provider                               |
      | xml     | sonata.ecommerce_demo.product.travel   |
      | json    | sonata.ecommerce_demo.product.travel   |
