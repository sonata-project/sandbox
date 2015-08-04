@frontend @product
Feature: Products
  In order to select some products
  As a visitor
  I want to be able to browse products

  Background:
    Given I am on "/"

  @product @400 @quantity
  Scenario: Check the update quantity AJAX call only allows XHR
    When I go to "shop/product/501/info?quantity=1"
    Then the response status code should be 400

  @product @200 @quantity
  Scenario Outline: Check the update quantity AJAX call
    Given I am an XHR request
    When I go to "shop/product/<identifier>/info?quantity=<quantity>"
    Then the response status code should be 200
    And the response is JSON
    And the price is <price>
    And the stock is <stock>

  Examples:
    | identifier | quantity | price   | stock |
    | 502        | 1        | 35.988  | 50    |
    | 502        | 3        | 107.964 | 50    |
    | 502        | 10       | 359.88  | 50    |
    | 503        | 1        | 9.99    | 10000 |

  @product @200 @quantity @variation @ok
  Scenario Outline: Check the variation choice form redirection AJAX call
    Given I am an XHR request
    When I go to "shop/product/<slug>/<identifier>/variation?sonata_product_variation_choices%5Btravellers%5D=<travellers>&sonata_product_variation_choices%5BtravelDays%5D=<traveldays>"
    Then the response status code should be 200
    And the response is JSON
    And the variation_url is "<variation_url>"

  Examples:
    | slug                | identifier | travellers | traveldays | variation_url                          |
    | travel-paris-tour-7 | 520        | 0          | 0          | /shop/product/travel-paris-tour-5/519 |
    | travel-paris-tour-5 | 519        | 1          | 0          | /shop/product/travel-paris-tour-7/520  |

  @product @200 @quantity @variation @ko
  Scenario Outline: Check the variation choice form redirection AJAX call error
    Given I am an XHR request
    When I go to "shop/product/travel-quebec-tour/<url>/variation?sonata_product_variation_choices%5Btravellers%5D=<travellers>&sonata_product_variation_choices%5BtravelDays%5D=<traveldays>"
    Then the response status code should be 200
    And the response is JSON
    And the error is "Sorry, the product you're looking for is unavailable."

  Examples:
    | url | travellers | traveldays |
    | 512 | 1          | 1          |

  @product @200 @catalog @ok
  Scenario: Check products & catalog page status code
    When I go to "shop/catalog"
    Then I should see "Catalog"
    And the response status code should be 200

  @product @200 @catalog @categories @ok
  Scenario Outline: Browse products by categories in catalog
    When I go to "shop/catalog"
    And I follow "<category>"
    Then I should see "<product>"
    And I should not see "No subcategories available"

  Examples:
    | category | product    |
    | Plushes  | PHP plush  |
    | Mugs     | PHP mug    |
    | Travels  | Japan tour |
    | Paris    | Paris tour |

  @200 @product @catalog @browser @ok
  Scenario Outline: Select a product by browsing products
    When I go to "shop/catalog"
    And I follow "<category>"
    And I follow "<product>"
    Then I should see "<description>"
    And the response status code should be 200

  Examples:
    | category | product          | description                      |
    | Plushes  | PHP plush        | Vincent Pontier                  |
    | Clothes  | PHP tee-shirt    | A nice PHP tee-shirt             |
    | Japan    | Japan tour       | Greater Tokyo Area               |
    | Travels  | Quebec tour      | Quebec tour for small group      |
    | Travels  | Paris tour       | Paris tour for small group       |
    | Travels  | Switzerland tour | Switzerland tour for small group |

  @product @200 @stock
  Scenario: Browse an "out of stock" product
    When I go to "shop/catalog"
    Then I follow "Clothes"
    Then I follow "PHP tee-shirt"
    Then the response status code should be 200
    And I should see "Warning : this product is currently out of stock !"

  @product @404
  Scenario: Browse a "disabled" product
    When I go to "shop/product/maximum-air-sonata-limited-edition/506"
    Then the response status code should be 404

  @product @200
  Scenario: Browse a product without a picture
    When I go to "shop/catalog"
    Then I follow "Shoes"
    Then I follow "Maximum Air Sonata ULTIMATE Edition"
    Then the response status code should be 200
    And I should see "Get this ULTIMATE edition"

  @product @404
  Scenario Outline: Check a non displayed product when the master product is disabled
    When I go to "shop/product/php-disabled-training/<id_product>"
    Then the response status code should be <status_code>

  Examples:
    | id_product | status_code |
    | 511        | 404         |
    | 512        | 404         |

  @product @200
  Scenario: Check display of the master product when having an active child
    When I go to "shop/catalog"
    Then I follow "Quebec"
    Then I follow "Quebec tour"
    Then the response status code should be 200
    And I should see "Quebec tour for small group"

  @product @404
  Scenario Outline: Check a non displayed product when having no active child
    When I go to "shop/product/php-disabled-child-training/<id_product>"
    Then the response status code should be <status_code>

  Examples:
    | id_product | status_code |
    | 516        | 404         |
    | 517        | 404         |

  @product @200 @date
  Scenario: Check the non display of starting date and related label if value is not set
    When I go to "shop/product/maximum-air-sonata-ultimate-edition/507"
    Then I should not see "Jan 16th, 2014"
    And I should not see "Starting date"