@frontend @product
Feature: Products
    In order to select some products
    As a visitor
    I want to be able to browse products

    Background:
        Given I am on "shop/basket"

    @400
    Scenario: Check the update quantity AJAX call only allows XHR
        When I go to "shop/product/1/info?quantity=1"
        Then the response status code should be 400

    @200
    Scenario: Check the update quantity AJAX call
        Given I am an XHR request
        When I go to "shop/product/1/info?quantity=1"
        Then the response status code should be 200
        And the response is JSON
        And the price is 29
        And the stock is 2000

    @200
    Scenario: Check the update quantity AJAX call
      Given I am an XHR request
      When I go to "shop/product/info/1/1"
      Then the response status code should be 200
      And the response is JSON
      And the price is 29
      And the stock is 2000

    @200
    Scenario: Check products & categories page status code
        When I go to "shop/category"
        Then I should see "Categories"
        And the response status code should be 200

    Scenario: Browse products by categories
        When I go to "shop/category"
        And I follow "Goodies"
        And I follow "Plushes"
        Then I should see "PHP plush"
        But I should not see "No subcategories available"

    Scenario: Select a product by browsing products
        When I go to "shop/category"
        And I follow "Goodies"
        And I follow "Plushes"
        And I follow "PHP plush"
        Then I should see "Vincent Pontier"

    @200
    Scenario: Browse out of stock product
        When I go to "shop/product/3/php-t-shirt"
        Then the response status code should be 200
        And I should see "Warning : this product is currently out of stock !"

    @404
    Scenario: Browse disabled product
        When I go to "shop/product/4/maximum-air-sonata-limited-edition"
        Then the response status code should be 404

    @200
    Scenario: Direct access to a product without a picture
        When I go to "shop/product/5/maximum-air-sonata-ultimate-edition"
        Then the response status code should be 200
        And I should see "Get this ULTIMATE edition"

    @404
    Scenario: Check non display when product master is disabled
        When I go to "shop/product/10/php-disabled-training"
        Then the response status code should be 404
        When I go to "shop/product/11/php-training-for-beginners"
        Then the response status code should be 404

    @200
    Scenario: Check display of master product when having an active child
        When I go to "shop/product/12/php-working-training"
        Then the response status code should be 200
        And I should not see "Warning : this product is currently out of stock !"
        But I should see "PHP working training for beginners"
        When I follow "PHP working training for beginners"
        Then the response status code should be 200
        And I should see "Warning : this product is currently out of stock !"

    @404
    Scenario: Check non display of a product when having no active child
        When I go to "shop/product/14/php-disabled-child-training"
        Then the response status code should be 404
        When I go to "shop/product/15/php-disabled-child-training"
        Then the response status code should be 404

    Scenario: Check the display of starting date if provided
        When I go to "shop/product/16/sonata-trainings"
        Then I should see "January 16, 2014 09:00"

    Scenario: Check the non display of starting date and related label if value is not set
        When I go to "shop/product/5/maximum-air-sonata-ultimate-edition"
        Then I should not see "January 16, 2014 09:00"
        And I should not see "Starting date"
