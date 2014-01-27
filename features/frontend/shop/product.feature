@frontend @product
Feature: Products
    In order to select some products
    As a visitor
    I want to be able to browse products

    Background:
        Given I am on "shop/basket"

    @400
    Scenario: Check the update quantity AJAX call only allows XHR
        When I go to "shop/product/501/info?quantity=1"
        Then the response status code should be 400

    @200
    Scenario: Check the update quantity AJAX call
        Given I am an XHR request
        When I go to "shop/product/501/info?quantity=1"
        Then the response status code should be 200
        And the response is JSON
        And the price is 35.988
        And the stock is 2000

    @200
    Scenario: Check the variation choice form redirection AJAX call
        Given I am an XHR request
        When I go to "shop/product/travel-quebec-tour/509/variation?sonata_product_variation_choices%5Btravellers%5D=1&sonata_product_variation_choices%5BtravelDays%5D=0"
        Then the response status code should be 200
        And the response is JSON
        And the variation_url is "/shop/product/travel-quebec-tour/510"

    @200
    Scenario: Check the variation choice form redirection AJAX call error
        Given I am an XHR request
      When I go to "shop/product/travel-quebec-tour/509/variation?sonata_product_variation_choices%5Btravellers%5D=1&sonata_product_variation_choices%5BtravelDays%5D=1"
        Then the response status code should be 200
        And the response is JSON
        And the error is "Sorry, the product you're looking for is unavailable."

    @200
    Scenario: Check products & catalog page status code
        When I go to "shop/catalog"
        Then I should see "Catalog"
        And the response status code should be 200

    Scenario: Browse products by categories in catalog
        When I go to "shop/catalog"
        And I follow "Plushes"
        Then I should see "PHP plush"
        But I should not see "No subcategories available"

    Scenario: Select a product by browsing products
        When I go to "shop/catalog"
        And I follow "Plushes"
        And I follow "PHP plush"
        Then I should see "Vincent Pontier"

    @200
    Scenario: Browse out of stock product
        When I go to "shop/product/php-t-shirt/505"
        Then the response status code should be 200
        And I should see "Warning : this product is currently out of stock !"

    @404
    Scenario: Browse disabled product
        When I go to "shop/product/maximum-air-sonata-limited-edition/506"
        Then the response status code should be 404

    @200
    Scenario: Direct access to a product without a picture
        When I go to "shop/product/maximum-air-sonata-ultimate-edition/507"
        Then the response status code should be 200
        And I should see "Get this ULTIMATE edition"

    @404
    Scenario: Check non display when product master is disabled
        When I go to "shop/product/php-disabled-training/512"
        Then the response status code should be 404
        When I go to "shop/product/php-training-for-beginners/511"
        Then the response status code should be 404

    @200
    Scenario: Check display of master product when having an active child
        When I go to "shop/product/travel-quebec-tour/508"
        Then the response status code should be 200
        And I should see "Quebec tour for small group"

    @404
    Scenario: Check non display of a product when having no active child
        When I go to "shop/product/php-disabled-child-training/516"
        Then the response status code should be 404
        When I go to "shop/product/php-disabled-child-training/517"
        Then the response status code should be 404

#    Scenario: Check the display of starting date if provided
#        When I go to "shop/product/sonata-trainings/518"
#        Then I should see "Jan 16th, 2014"

    Scenario: Check the non display of starting date and related label if value is not set
        When I go to "shop/product/maximum-air-sonata-ultimate-edition/507"
        Then I should not see "Jan 16th, 2014"
        And I should not see "Starting date"

    Scenario: Access to travel list
        When I go to "shop/catalog"
        And I follow "Travels"
        And I follow "Quebec tour"
        Then the response status code should be 200
        And I should see "Quebec tour for small group"

    Scenario: Direct access to a travel
        When I go to "shop/catalog"
        And I follow "Travels"
        And I follow "Paris tour"
        Then the response status code should be 200
        And I should see "Paris tour for small group"

    Scenario: Direct access to a travel
        When I go to "shop/catalog"
        And I follow "Travels"
        And I follow "Switzerland tour"
        Then the response status code should be 200
        And I should see "Switzerland tour for small group"

    Scenario: Browse travels
        When I go to "shop/catalog/travels/2"
        Then I should see "Quebec tour"
        And I should see "Paris tour"
        And I should see "Switzerland tour"

    Scenario: Browse travels
        When I go to "shop/catalog/travel"
        And I follow "Europe"
        Then I should see "France"
        Then I should see "Switzerland"
